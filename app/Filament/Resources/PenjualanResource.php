<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';

    protected static ?string $navigationLabel = 'Sale';

    protected static ?string $recordTitleAttribute = 'Nota';

    public static function getNavigationGroup(): ?string
    {
        return 'Transaction Management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Nota')
                    ->required()
                    ->maxLength(10)
                    ->label('Nota')
                    ->unique(ignoreRecord: true)
                    ->placeholder('NPJ001')
                    ->columnSpan(1),
                DatePicker::make('TglNota')
                    ->required()
                    ->label('Date')
                    ->columnSpan(1),
                Select::make('KdPelanggan')
                    ->required()
                    ->label('Customer')
                    ->options(function () {
                        return Pelanggan::all()->pluck('NmPelanggan', 'KdPelanggan');
                    })
                    ->searchable()
                    ->placeholder('Select customer')
                    ->columnSpan(1),
                TextInput::make('Diskon')
                    ->required()
                    ->numeric()
                    ->label('Discount')
                    ->placeholder('0')
                    ->minValue(0),
                Repeater::make('penjualan_detail')
                    ->label('Medicine Detail')
                    ->schema([
                        Select::make('KdObat')
                            ->label('Name')
                            ->options(Obat::all()->pluck('NmObat', 'KdObat'))
                            ->searchable()
                            ->required(),
                        TextInput::make('Quantity')
                            ->numeric()
                            ->required()
                            ->label('Quantity'),
                    ])
                    ->addActionLabel('Tambah Obat')
                    ->columns(2)
                    ->required()
                    ->columnSpan(2),
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
                    ->label('Date')
                    ->sortable(),
                TextColumn::make('obat.NmObat')
                    ->label('Medicine')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('Jumlah')
                    ->getStateUsing(fn($record) => $record->obat->first()->pivot->Jumlah ?? 'N/A')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('pelanggan.NmPelanggan')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Diskon')
                    ->label('Discount')
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->Diskon . '%'),
            ])
            ->filters([
                SelectFilter::make('KdPelanggan')
                    ->label('Filter by Customer')
                    ->relationship('pelanggan', 'NmPelanggan'),
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
            ->defaultSort('Nota')
            ->striped();;
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
