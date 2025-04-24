<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObatResource\Pages;
use App\Filament\Resources\ObatResource\RelationManagers;
use App\Models\Obat;
use App\Models\Suplier;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Medicines';

    protected static ?string $recordTitleAttribute = 'NmObat';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Inventory Management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('KdObat')
                    ->required()
                    ->maxLength(10)
                    ->label('Medicine Code')
                    ->unique(ignoreRecord: true)
                    ->placeholder('OBT001')
                    ->columnSpan(1),

                TextInput::make('NmObat')
                    ->required()
                    ->maxLength(255)
                    ->label('Medicine Name')
                    ->placeholder('Paracetamol 500mg')
                    ->columnSpan(1),

                Select::make('Jenis')
                    ->required()
                    ->label('Type')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Capsule',
                        'Sirup' => 'Syrup',
                        'Salep' => 'Ointment',
                        'Injeksi' => 'Injection',
                        'Tetes' => 'Drops',
                        'Lain-lain' => 'Others'
                    ])
                    ->placeholder('Select medicine type'),

                TextInput::make('Satuan')
                    ->required()
                    ->maxLength(50)
                    ->label('Unit')
                    ->placeholder('Strip'),

                TextInput::make('HargaBeli')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Purchase Price')
                    ->placeholder('10000')
                    ->minValue(0),

                TextInput::make('HargaJual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Selling Price')
                    ->placeholder('12000')
                    ->minValue(0),

                TextInput::make('Stok')
                    ->required()
                    ->numeric()
                    ->label('Stock')
                    ->placeholder('100')
                    ->minValue(0),

                Select::make('KdSuplier')
                    ->required()
                    ->label('Supplier')
                    ->options(function () {
                        return Suplier::all()->pluck('NmSuplier', 'KdSuplier');
                    })
                    ->searchable()
                    ->placeholder('Select supplier')
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KdObat')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NmObat')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('Jenis')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Satuan')
                    ->label('Unit'),

                TextColumn::make('HargaBeli')
                    ->label('Purchase Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('HargaJual')
                    ->label('Selling Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('Stok')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(
                        fn(int $state): string =>
                        $state <= 10 ? 'danger' : ($state <= 25 ? 'warning' : 'success')
                    ),

                TextColumn::make('suplier.NmSuplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('Jenis')
                    ->label('Filter by Type')
                    ->options([
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Capsule',
                        'Sirup' => 'Syrup',
                        'Salep' => 'Ointment',
                        'Injeksi' => 'Injection',
                        'Tetes' => 'Drops',
                        'Lain-lain' => 'Others'
                    ]),

                SelectFilter::make('KdSuplier')
                    ->label('Filter by Supplier')
                    ->relationship('suplier', 'NmSuplier'),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn(Builder $query): Builder => $query->where('Stok', '<=', 25)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make(),
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
}
