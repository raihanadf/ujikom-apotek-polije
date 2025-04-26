<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PenjualanExporter;
use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Obat;
use App\Models\Penjualan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                DatePicker::make('TglNota')
                    ->required()
                    ->label('Date')
                    ->default(now())
                    ->columnSpan(1),
                Select::make('KdPelanggan')
                    ->required()
                    ->label('Customer')
                    ->relationship('pelanggan', 'NmPelanggan')
                    ->preload()
                    ->searchable()
                    ->placeholder('Select customer')
                    ->columnSpan(1),
                TextInput::make('Diskon')
                    ->required()
                    ->numeric()
                    ->label('Discount')
                    ->placeholder('0')
                    ->default(0)
                    ->minValue(0),
                TextInput::make('TotalHargaKeseluruhan')
                    ->label('Total Price')
                    ->disabled()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                Repeater::make('penjualan_detail')
                    ->label('Medicine Detail')
                    ->schema([
                        Select::make('KdObat')
                            ->label('Name')
                            ->options(Obat::all()->pluck('NmObat', 'KdObat'))
                            ->searchable()
                            ->required(),
                        TextInput::make('Jumlah')
                            ->numeric()
                            ->required()
                            ->label('Quantity')
                            ->minValue(1)
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, $fail) use ($get) {
                                    $obatId = $get('KdObat');
                                    if ($obatId) {
                                        $obat = Obat::find($obatId);
                                        if ($obat && $value > $obat->Stok) {
                                            $fail("Penjualan gagal: Stok {$obat->NmObat} hanya tersedia {$obat->Stok}.");
                                        }
                                    }
                                };
                            }),
                    ])
                    ->addActionLabel('Add Medicine')
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
                    ->date()
                    ->sortable(),
                TextColumn::make('pelanggan.NmPelanggan')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Diskon')
                    ->label('Discount')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state . '%'),
                TextColumn::make('obat_count')
                    ->label('Items')
                    ->counts('obat')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('KdPelanggan')
                    ->label('Filter by Customer')
                    ->relationship('pelanggan', 'NmPelanggan'),
                Filter::make('big_sales')
                    ->label('Big Sales')
                    ->query(fn(Builder $query): Builder => $query->whereHas('obat', fn(Builder $query) => $query->where('penjualan_detail.Jumlah', '>=', 10)))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(PenjualanExporter::class)
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'view' => Pages\ViewPenjualan::route('/{record}'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
