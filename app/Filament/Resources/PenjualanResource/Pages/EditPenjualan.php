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
        // Ambil data penjualan detail sebelum perubahan
        $originalDetails = $this->record->obat->mapWithKeys(function ($obat) {
            return [$obat->KdObat => $obat->pivot->Jumlah];
        });
    
        // Data baru dari form
        $newDetails = collect($this->penjualanDetail)->mapWithKeys(function ($item) {
            return [$item['KdObat'] => $item['Jumlah']];
        });
    
        // Perbarui stok berdasarkan perubahan
        foreach ($newDetails as $kdObat => $newJumlah) {
            $originalJumlah = $originalDetails[$kdObat] ?? 0; // Jumlah sebelum perubahan
            $selisih = $newJumlah - $originalJumlah; // Hitung selisih
    
            $obat = \App\Models\Obat::find($kdObat);
            if ($obat) {
                $obat->decrement('Stok', $selisih); // Kurangi stok jika selisih positif
            }
        }
    
        // Kembalikan stok untuk obat yang dihapus dari penjualan
        foreach ($originalDetails as $kdObat => $originalJumlah) {
            if (!$newDetails->has($kdObat)) {
                $obat = \App\Models\Obat::find($kdObat);
                if ($obat) {
                    $obat->increment('Stok', $originalJumlah); // Kembalikan stok
                }
            }
        }
    
        // Sinkronkan data baru ke tabel pivot
        $syncData = $newDetails->mapWithKeys(function ($jumlah, $kdObat) {
            return [$kdObat => ['Jumlah' => $jumlah]];
        })->toArray();
    
        $this->record->obat()->sync($syncData);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
