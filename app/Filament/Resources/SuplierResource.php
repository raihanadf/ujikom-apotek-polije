<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuplierResource\Pages;
use App\Models\Suplier;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class SuplierResource extends Resource
{
    protected static ?string $model = Suplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Suppliers';

    protected static ?string $recordTitleAttribute = 'NmSuplier';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Inventory Management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('KdSuplier')
                    ->required()
                    ->maxLength(10)
                    ->label('Supplier Code')
                    ->unique(ignoreRecord: true)
                    ->placeholder('SUP001')
                    ->helperText('Enter a unique supplier code (e.g., SUP001)'),

                TextInput::make('NmSuplier')
                    ->required()
                    ->maxLength(255)
                    ->label('Supplier Name')
                    ->placeholder('PT Pharma Indonesia'),

                Textarea::make('Alamat')
                    ->maxLength(255)
                    ->label('Address')
                    ->placeholder('Jl. Industri No. 123')
                    ->columnSpanFull(),

                TextInput::make('Kota')
                    ->maxLength(100)
                    ->label('City')
                    ->placeholder('Jakarta'),

                TextInput::make('Telpon')
                    ->tel()
                    ->maxLength(20)
                    ->label('Phone Number')
                    ->placeholder('021-5551234')
                    ->helperText('Format: 021-5551234'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KdSuplier')
                    ->label('Supplier Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NmSuplier')
                    ->label('Supplier Name')
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
                    ->options(fn() => Suplier::distinct()->pluck('Kota', 'Kota')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('NmSuplier')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupliers::route('/'),
            'create' => Pages\CreateSuplier::route('/create'),
            'edit' => Pages\EditSuplier::route('/{record}/edit'),
        ];
    }
}
