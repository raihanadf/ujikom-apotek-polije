<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function afterCreate(): void
    {
        $detailObat = $this->form->getState()['pembelian_detail'] ?? [];

        foreach ($detailObat as $item) {
            $this->record->obat()->attach($item['KdObat'], ['Jumlah' => $item['Jumlah']]);
        }
    }
}
