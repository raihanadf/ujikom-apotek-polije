<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $penjualanDetail = $this->record->obat->map(function ($obat) {
            return [
                'KdObat' => $obat->KdObat,
                'Jumlah' => $obat->pivot->Jumlah,
            ];
        })->toArray();

        $data['penjualan_detail'] = $penjualanDetail;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->penjualanDetail = $data['penjualan_detail'] ?? [];
        unset($data['penjualan_detail']);

        return $data;
    }

    protected function afterSave(): void
    {
        $syncData = [];
        foreach ($this->penjualanDetail as $item) {
            $syncData[$item['KdObat']] = ['Jumlah' => $item['Jumlah']];
        }

        $this->record->obat()->sync($syncData);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
