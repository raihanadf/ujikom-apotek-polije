<?php

namespace App\Filament\Exports;

use App\Models\Penjualan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PenjualanExporter extends Exporter
{
    protected static ?string $model = Penjualan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('Nota')
                ->label('Nomor Nota'),
            ExportColumn::make('TglNota')
                ->label('Tanggal Penjualan')
                ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '-'),
            ExportColumn::make('pelanggan.NmPelanggan')
                ->label('Nama Pelanggan'),
            ExportColumn::make('pelanggan.Alamat')
                ->label('Alamat Pelanggan'),
            ExportColumn::make('Diskon')
                ->label('Diskon (%)')
                ->formatStateUsing(fn($state) => $state . '%'),
            ExportColumn::make('obat_count')
                ->label('Jumlah Item')
                ->getStateUsing(function (Penjualan $record): int {
                    return $record->obat()->count();
                }),
            ExportColumn::make('total_harga')
                ->label('Total Harga (Rp)')
                ->getStateUsing(function (Penjualan $record): string {
                    $total = $record->TotalHargaKeseluruhan ?? 0;
                    return 'Rp ' . number_format($total, 0, ',', '.');
                }),
            ExportColumn::make('created_at')
                ->label('Tanggal Dibuat')
                ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d/m/Y H:i') : '-'),
        ];
    }

    public static function getHeaderLabels(): array
    {
        return [
            'Nota' => 'Nomor Nota',
            'TglNota' => 'Tanggal Penjualan',
            'pelanggan.NmPelanggan' => 'Nama Pelanggan',
            'pelanggan.Alamat' => 'Alamat Pelanggan',
            'Diskon' => 'Diskon (%)',
            'obat_count' => 'Jumlah Item',
            'total_harga' => 'Total Harga (Rp)',
            'created_at' => 'Tanggal Dibuat',
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your penjualan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';
        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }
        return $body;
    }

    public static function afterExport(Export $export): StreamedResponse
    {
        return response()->streamDownload(
            function () use ($export) {
                echo file_get_contents(storage_path('app/filament-exports/' . $export->file_name));
            },
            $export->file_name,
            ['Content-Type' => 'text/csv']
        );
    }
}
