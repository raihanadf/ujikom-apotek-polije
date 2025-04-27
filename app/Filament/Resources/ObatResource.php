<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObatResource\Pages;
use App\Filament\Resources\ObatResource\RelationManagers;
use App\Models\Obat;
use App\Models\Suplier;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Obat';

    protected static ?string $recordTitleAttribute = 'NmObat';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Inventaris';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('KdObat')
                    ->required()
                    ->maxLength(10)
                    ->label('Kode Obat')
                    ->unique(ignoreRecord: true)
                    ->placeholder('OBT001')
                    ->columnSpan(1),

                TextInput::make('NmObat')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Obat')
                    ->placeholder('Paracetamol 500mg')
                    ->columnSpan(1),

                Select::make('Jenis')
                    ->required()
                    ->label('Jenis')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Kapsul',
                        'Sirup' => 'Sirup',
                        'Salep' => 'Salep',
                        'Injeksi' => 'Injeksi',
                        'Tetes' => 'Tetes',
                        'Lain-lain' => 'Lain-lain'
                    ])
                    ->placeholder('Pilih jenis obat'),

                TextInput::make('Satuan')
                    ->required()
                    ->maxLength(50)
                    ->label('Satuan')
                    ->placeholder('Strip'),

                TextInput::make('HargaBeli')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Beli')
                    ->placeholder('10000')
                    ->minValue(0),

                TextInput::make('HargaJual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Jual')
                    ->placeholder('12000')
                    ->minValue(0),

                TextInput::make('Stok')
                    ->required()
                    ->numeric()
                    ->label('Stok')
                    ->placeholder('100')
                    ->minValue(0),

                DateTimePicker::make('TglKadaluarsa')
                    ->required()
                    ->label('Tanggal Kadaluarsa')
                    ->minDate(now())
                    ->displayFormat('d/m/Y')
                    ->native(false),

                Select::make('KdSuplier')
                    ->required()
                    ->label('Supplier')
                    ->options(function () {
                        return Suplier::all()->pluck('NmSuplier', 'KdSuplier');
                    })
                    ->searchable()
                    ->placeholder('Pilih supplier')
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KdObat')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NmObat')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('Jenis')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Satuan')
                    ->label('Satuan'),

                TextColumn::make('HargaBeli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('HargaJual')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('Stok')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->color(
                        fn(int $state): string =>
                        $state <= 10 ? 'danger' : ($state <= 25 ? 'warning' : 'success')
                    ),

                TextColumn::make('TglKadaluarsa')
                    ->label('Kadaluarsa')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(
                        fn($record): string =>
                        Carbon::parse($record->TglKadaluarsa)->lte(Carbon::now())
                            ? 'danger'
                            : (Carbon::parse($record->TglKadaluarsa)->lte(Carbon::now()->addDays(7))
                                ? 'warning'
                                : 'success')
                    ),

                TextColumn::make('suplier.NmSuplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('Jenis')
                    ->label('Filter berdasarkan Jenis')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Kapsul',
                        'Sirup' => 'Sirup',
                        'Salep' => 'Salep',
                        'Injeksi' => 'Injeksi',
                        'Tetes' => 'Tetes',
                        'Lain-lain' => 'Lain-lain'
                    ]),

                SelectFilter::make('KdSuplier')
                    ->label('Filter berdasarkan Supplier')
                    ->relationship('suplier', 'NmSuplier'),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stok Rendah')
                    ->query(fn(Builder $query): Builder => $query->where('Stok', '<=', 25)),

                // Filter untuk obat yang akan kadaluarsa dalam 7 hari
                Filter::make('akan_kadaluarsa')
                    ->label('Akan Kadaluarsa (7 Hari)')
                    ->query(function (Builder $query): Builder {
                        $oneWeekFromNow = Carbon::now()->addDays(7);
                        return $query
                            ->where('TglKadaluarsa', '>=', Carbon::now())
                            ->where('TglKadaluarsa', '<=', $oneWeekFromNow);
                    })
                    ->indicator('Akan Kadaluarsa'),

                // Filter untuk obat yang sudah kadaluarsa
                Filter::make('sudah_kadaluarsa')
                    ->label('Sudah Kadaluarsa')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('TglKadaluarsa', '<', Carbon::now())
                    )
                    ->indicator('Sudah Kadaluarsa'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus'),
                ]),
            ])
            ->defaultSort('NmObat')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // No direct relations displayed here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObats::route('/'),
            'create' => Pages\CreateObat::route('/create'),
            'edit' => Pages\EditObat::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role == "admin" || auth()->user()->role == "apoteker";
    }
}
