<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPenjualan extends ViewRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $penjualanDetail = $this->record->obat->map(function ($obat) {
            return [
                'KdObat' => $obat->KdObat,
                'Jumlah' => $obat->pivot->Jumlah,
                'HargaJual' => $obat->HargaJual,
                'TotalHarga' => $obat->pivot->Jumlah * $obat->HargaJual,
            ];
        })->toArray();

        $data['penjualan_detail'] = $penjualanDetail;
        $totalHarga = collect($penjualanDetail)->sum('TotalHarga');
        $diskon = $this->record->Diskon ?? 0;
        $data['TotalHargaKeseluruhan'] = $totalHarga - ($totalHarga * $diskon / 100);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
