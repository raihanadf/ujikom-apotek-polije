<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\PenjualanExporter;
use Filament\Tables\Actions\ExportAction;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';

    protected static ?string $navigationLabel = 'Penjualan';

    protected static ?string $recordTitleAttribute = 'Nota';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Transaksi';
    }

    public static function form(Form $form): Form
    {
        // Existing form code remains unchanged
        return $form
            ->schema([
                DatePicker::make('TglNota')
                    ->required()
                    ->label('Tanggal')
                    ->default(now())
                    ->columnSpan(1),
                Select::make('KdPelanggan')
                    ->required()
                    ->label('Pelanggan')
                    ->options(function () {
                        return Pelanggan::all()->pluck('NmPelanggan', 'KdPelanggan');
                    })
                    ->searchable()
                    ->placeholder('Pilih pelanggan')
                    ->columnSpan(1),
                TextInput::make('Diskon')
                    ->required()
                    ->numeric()
                    ->label('Diskon')
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
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('pelanggan.NmPelanggan')
                    ->label('Pelanggan')
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
                SelectFilter::make('KdPelanggan')
                    ->label('Filter berdasarkan Pelanggan')
                    ->relationship('pelanggan', 'NmPelanggan'),
                Filter::make('big_sales')
                    ->label('Penjualan Besar')
                    ->query(fn(Builder $query): Builder => $query->whereHas('obat', fn(Builder $query) => $query->where('penjualan_detail.Jumlah', '>=', 10))),

                // Filter berdasarkan rentang tanggal
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('TglNota', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('TglNota', '<=', $date),
                            );
                    })
                    ->label('Rentang Tanggal')
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari: ' . $data['dari_tanggal'];
                        }

                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . $data['sampai_tanggal'];
                        }

                        return $indicators;
                    }),
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
            ->headerActions([
                ExportAction::make()
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

    public static function canViewAny(): bool
    {
        return auth()->user()->role == "admin" || auth()->user()->role == "apoteker";
    }
}
