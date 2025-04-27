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

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $recordTitleAttribute = 'NmPelanggan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Pelanggan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('KdPelanggan')
                    ->required()
                    ->maxLength(10)
                    ->label('Kode Pelanggan')
                    ->unique(ignoreRecord: true)
                    ->placeholder('PLG001')
                    ->helperText('Masukkan kode pelanggan unik (contoh: PLG001)'),

                TextInput::make('NmPelanggan')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Pelanggan')
                    ->placeholder('Rumah Sakit Medika'),

                Textarea::make('Alamat')
                    ->maxLength(255)
                    ->label('Alamat')
                    ->placeholder('Jl. Pahlawan No. 10')
                    ->columnSpanFull(),

                TextInput::make('Kota')
                    ->maxLength(100)
                    ->label('Kota')
                    ->placeholder('Jakarta'),

                TextInput::make('Telpon')
                    ->tel()
                    ->maxLength(20)
                    ->label('Nomor Telepon')
                    ->placeholder('021-9876543'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KdPelanggan')
                    ->label('Kode Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NmPelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('Kota')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Telpon')
                    ->label('Telepon')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Diperbarui Pada')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Kota')
                    ->label('Filter Berdasarkan Kota')
                    ->options(fn() => Pelanggan::distinct()->pluck('Kota', 'Kota')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus'),
                    Tables\Actions\ExportBulkAction::make()->label('Ekspor'),
                ])->label('Aksi Massal'),
            ])
            ->defaultSort('NmPelanggan')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role == "admin";
    }
}
