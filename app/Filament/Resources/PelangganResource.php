<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $recordTitleAttribute = 'NmPelanggan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Customer Management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('KdPelanggan')
                    ->required()
                    ->maxLength(10)
                    ->label('Customer Code')
                    ->unique(ignoreRecord: true)
                    ->placeholder('PLG001')
                    ->helperText('Enter a unique customer code (e.g., PLG001)'),

                TextInput::make('NmPelanggan')
                    ->required()
                    ->maxLength(255)
                    ->label('Customer Name')
                    ->placeholder('Rumah Sakit Medika'),

                Textarea::make('Alamat')
                    ->maxLength(255)
                    ->label('Address')
                    ->placeholder('Jl. Pahlawan No. 10')
                    ->columnSpanFull(),

                TextInput::make('Kota')
                    ->maxLength(100)
                    ->label('City')
                    ->placeholder('Jakarta'),

                TextInput::make('Telpon')
                    ->tel()
                    ->maxLength(20)
                    ->label('Phone Number')
                    ->placeholder('021-9876543'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KdPelanggan')
                    ->label('Customer Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NmPelanggan')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Alamat')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('Kota')
                    ->label('City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Telpon')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Kota')
                    ->label('Filter by City')
                    ->options(fn() => Pelanggan::distinct()->pluck('Kota', 'Kota')->toArray()),
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
            ->defaultSort('NmPelanggan')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // No relations for now, could add penjualan relations if needed later
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}
