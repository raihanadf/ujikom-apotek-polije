<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions;
use App\Models\Obat;
use Filament\Resources\Pages\EditRecord;

class EditPembelian extends EditRecord
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pembelianDetail = $data['pembelian_detail'] ?? [];
        unset($data['pembelian_detail']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Mengambil data sebelum perubahan
        $originalDetails = $this->record->obat->mapWithKeys(function ($obat) {
            return [$obat->KdObat => $obat->pivot->Jumlah];
        });

        // Mengambil data baru
        $newDetails = collect($this->pembelianDetail)->mapWithKeys(function ($item) {
            return [$item['KdObat'] => $item['Jumlah']];
        });

        // Memperbarui stock
        foreach ($newDetails as $KdObat => $newJumlah) {
            $originalJumlah = $originalDetails[$KdObat] ?? 0;
            $selisih = $newJumlah - $originalJumlah;

            $obat = Obat::find($KdObat);
            if ($obat) {
                $obat->increment('Stok', $selisih);
            }
        }

        // Mengembalikan stock untuk obat yang dihapus
        foreach ($originalDetails as $KdObat => $originalJumlah) {
            if (!$newDetails->has($KdObat)) {
                $obat = Obat::find($KdObat);
                if ($obat) {
                    $obat->decrement('Stok', $originalJumlah);
                }
            }
        }

        // Mensinkronkan stock
        $syncData = $newDetails->mapWithKeys(function ($jumlah, $KdObat) {
            return [$KdObat => ['Jumlah' => $jumlah]];
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
