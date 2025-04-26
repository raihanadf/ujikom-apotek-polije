<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Suplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $navigationLabel = 'Pembelian';

    protected static ?string $recordTitleAttribute = 'Nota';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Transaksi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('TglNota')
                    ->required()
                    ->label('Tanggal')
                    ->columnSpan(1)
                    ->default(now()),
                Select::make('KdSuplier')
                    ->required()
                    ->label('Supplier')
                    ->options(function () {
                        return Suplier::all()->pluck('NmSuplier', 'KdSuplier');
                    })
                    ->searchable()
                    ->placeholder('Pilih supplier')
                    ->columnSpan(1),
                TextInput::make('Diskon')
                    ->required()
                    ->numeric()
                    ->label('Diskon')
                    ->placeholder('0')
                    ->default(0)
                    ->minValue(0),
                Repeater::make('pembelian_detail')
                    ->label('Detail Obat')
                    ->schema([
                        Select::make('KdObat')
                            ->label('Nama')
                            ->options(Obat::all()->pluck('NmObat', 'KdObat'))
                            ->searchable()
                            ->required(),
                        TextInput::make('Jumlah')
                            ->numeric()
                            ->required()
                            ->label('Jumlah')
                            ->minValue(1),
                    ])
                    ->addActionLabel('Tambah Obat')
                    ->columns(2)
                    ->required()
                    ->columnSpan(2),
                TextInput::make('TotalHargaKeseluruhan')
                    ->label('Total Price')
                    ->disabled()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Nota')
                    ->label('Nota')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('TglNota')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('suplier.NmSuplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Diskon')
                    ->label('Diskon')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state . '%'),
                TextColumn::make('obat_count')
                    ->label('Item')
                    ->counts('obat')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('KdSuplier')
                    ->label('Filter berdasarkan Supplier')
                    ->relationship('suplier', 'NmSuplier'),

                Filter::make('big_purchases')
                    ->label('Pembelian Besar')
                    ->query(fn(Builder $query): Builder => $query->whereHas('obat', fn(Builder $query) => $query->where('pembelian_detail.Jumlah', '>=', 100)))
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
            ->defaultSort('Nota', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'view' => Pages\ViewPembelian::route('/{record}'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}
