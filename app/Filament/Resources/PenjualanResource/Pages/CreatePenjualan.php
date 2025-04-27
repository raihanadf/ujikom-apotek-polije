<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use App\Models\Penjualan;
use App\Models\Obat;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;
    public $penjualanDetail;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['Nota'] = $this->generateNextNotaNumber();
        $this->penjualanDetail = $data['penjualan_detail'] ?? [];

        foreach ($this->penjualanDetail as $detail) {
            if (isset($detail['KdObat'])) {
                $obat = \App\Models\Obat::where('KdObat', $detail['KdObat'])->first();

                if ($obat && $obat->TglKadaluarsa < now()) {
                    // Show notification
                    Notification::make()
                        ->title("Expired Medication")
                        ->body("Obat {$detail['KdObat']} udah expired di tanggal {$obat->TglKadaluarsa}.")
                        ->danger()
                        ->send();

                    // Halt the execution and redirect back
                    $this->halt();
                    return $data; // This return won't actually happen due to the halt()
                }
            }
        }

        unset($data['penjualan_detail']);
        return $data;
    }
    protected function afterCreate(): void
    {
        foreach ($this->penjualanDetail as $item) {
            $this->record->obat()->attach($item['KdObat'], ['Jumlah' => $item['Jumlah']]);

            // Mengurangi Stock
            $obat = Obat::find($item['KdObat']);
            if ($obat) {
                $obat->decrement('Stok', $item['Jumlah']);
            }
        }
    }

    protected function generateNextNotaNumber(): string
    {
        $latestNota = Penjualan::orderBy('Nota', 'desc')
            ->where('Nota', 'like', 'NPJ%')
            ->first();

        if (!$latestNota) {
            return 'NPJ001';
        }

        $numericPart = (int) substr($latestNota->Nota, 3);

        $nextNumericPart = $numericPart + 1;

        // will handle npj001-npj999, npj1000 and beyond
        return 'NPJ' . str_pad($nextNumericPart, 3, '0', STR_PAD_LEFT);
    }
}
