<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use App\Models\Pembelian;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['Nota'] = $this->generateNextNotaNumber();

        $this->pembelianDetail = $data['pembelian_detail'] ?? [];
        unset($data['pembelian_detail']);

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->pembelianDetail as $item) {
            $this->record->obat()->attach($item['KdObat'], ['Jumlah' => $item['Jumlah']]);
        }
    }

    protected function generateNextNotaNumber(): string
    {
        $latestNota = Pembelian::orderBy('Nota', 'desc')
            ->where('Nota', 'like', 'NPB%')
            ->first();

        if (!$latestNota) {
            return 'NPB001';
        }

        $numericPart = (int) substr($latestNota->Nota, 3);
        $nextNumericPart = $numericPart + 1;

        // will handle npb001-npb999, npb1000 and beyond
        return 'NPB' . str_pad($nextNumericPart, 3, '0', STR_PAD_LEFT);
    }
}
