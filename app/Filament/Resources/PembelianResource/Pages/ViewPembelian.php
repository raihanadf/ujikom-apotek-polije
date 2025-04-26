<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPembelian extends ViewRecord
{
    protected static string $resource = PembelianResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pembelianDetail = $this->record->obat->map(function ($obat) {
            return [
                'KdObat' => $obat->KdObat,
                'Jumlah' => $obat->pivot->Jumlah,
                'HargaBeli' => $obat->HargaBeli,
                'TotalHarga' => $obat->pivot->Jumlah * $obat->HargaBeli,
            ];
        })->toArray();

        $data['pembelian_detail'] = $pembelianDetail;

        // Hitung total harga keseluruhan
        $totalHarga = collect($pembelianDetail)->sum('TotalHarga');
        $diskon = $this->record->Diskon ?? 0;
        $data['TotalHargaKeseluruhan'] = $totalHarga - ($totalHarga * $diskon / 100);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(PembelianResource::getUrl()),
        ];
    }
}
