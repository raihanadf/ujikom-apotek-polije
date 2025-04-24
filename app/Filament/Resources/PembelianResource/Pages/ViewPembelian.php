<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions;
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
            ];
        })->toArray();

        $data['pembelian_detail'] = $pembelianDetail;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
