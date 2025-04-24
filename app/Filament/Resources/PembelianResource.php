<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Filament\Resources\PembelianResource\RelationManagers;
use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Suplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $navigationLabel = 'Purchase';

    protected static ?string $recordTitleAttribute = 'Nota';

    public static function getNavigationGroup(): ?string
    {
        return 'Transaction Management';
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['pembelian_detail']);
        return $data;
    }

    public static function mutateFormDataBeforeUpdate(array $data): array
    {
        unset($data['pembelian_detail']);
        return $data;
    }

    protected function handleRecordCreation(array $data): Pembelian
    {
        $detailData = $data['pembelian_detail'] ?? [];
        unset($data['pembelian_detail']);

        $pembelian = Pembelian::create($data);

        foreach ($detailData as $detail) {
            $pembelian->obat()->attach($detail['KdObat'], ['Jumlah' => $detail['Jumlah']]);
        }

        return $pembelian;
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
                    ->placeholder('NPB001')
                    ->columnSpan(1),
                DatePicker::make('TglNota')
                    ->required()
                    ->label('Date')
                    ->columnSpan(1),
                Select::make('KdSuplier')
                    ->required()
                    ->label('Supplier')
                    ->options(function () {
                        return Suplier::all()->pluck('NmSuplier', 'KdSuplier');
                    })
                    ->searchable()
                    ->placeholder('Select supplier')
                    ->columnSpan(1),
                TextInput::make('Diskon')
                    ->required()
                    ->numeric()
                    ->label('Discount')
                    ->placeholder('0')
                    ->minValue(0),
                Repeater::make('obat')
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
                TextColumn::make('suplier.NmSuplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Diskon')
                    ->label('Discount')
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->Diskon . '%'),
            ])
            ->filters([
                SelectFilter::make('KdSuplier')
                    ->label('Filter by Supplier')
                    ->relationship('suplier', 'NmSuplier'),

                Filter::make('big_purchases')
                    ->label('Big Purchases')
                    ->query(fn(Builder $query): Builder => $query->whereHas('obat', fn(Builder $query) => $query->where('pembelian_detail.Jumlah', '>=', 100)))
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
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}
