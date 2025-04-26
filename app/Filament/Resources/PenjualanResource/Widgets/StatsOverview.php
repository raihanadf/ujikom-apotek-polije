<?php

namespace App\Filament\Resources\PenjualanResource\Widgets;

use App\Models\Penjualan;
use App\Models\Obat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $dailyTransactions = Penjualan::select(
            DB::raw('DATE(TglNota) as date'),
            DB::raw('COUNT(Nota) as count')
        )
            ->whereBetween('TglNota', [Carbon::now()->subDays(6), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $currentWeekCount = Penjualan::whereBetween('TglNota', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();

        $previousWeekCount = Penjualan::whereBetween('TglNota', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek(),
        ])->count();

        $transactionChartData = array_fill(0, 7, 0);

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
            foreach ($dailyTransactions as $transaction) {
                if ($transaction->date == $date) {
                    $transactionChartData[$i] = $transaction->count;
                    break;
                }
            }
        }

        $topMedicines = DB::table('penjualan_detail')
            ->select('KdObat', DB::raw('SUM(Jumlah) as total'))
            ->join('penjualans', 'penjualan_detail.Nota', '=', 'penjualans.Nota')
            ->whereMonth('TglNota', Carbon::now()->month)
            ->whereYear('TglNota', Carbon::now()->year)
            ->groupBy('KdObat')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $medicineChartData = $topMedicines->pluck('total')->toArray();
        $topMedicineCode = $topMedicines->count() > 0 ? $topMedicines->first()->KdObat : '-';
        $topMedicineTotal = $topMedicines->count() > 0 ? $topMedicines->first()->total : 0;

        $monthlyCounts = [];
        for ($i = 2; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Penjualan::whereYear('TglNota', $date->year)
                ->whereMonth('TglNota', $date->month)
                ->count();
            $monthlyCounts[] = $count;
        }

        return [
            Stat::make('Transaksi Minggu Ini', $currentWeekCount)
                ->description($previousWeekCount > 0
                    ? (($currentWeekCount - $previousWeekCount) / $previousWeekCount * 100) >= 0
                    ? 'Naik ' . number_format(abs(($currentWeekCount - $previousWeekCount) / $previousWeekCount * 100), 1) . '%'
                    : 'Turun ' . number_format(abs(($currentWeekCount - $previousWeekCount) / $previousWeekCount * 100), 1) . '%'
                    : '')
                ->descriptionIcon($currentWeekCount >= $previousWeekCount ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($currentWeekCount >= $previousWeekCount ? 'success' : 'danger')
                ->chart($transactionChartData),

            Stat::make('Obat Terlaris Bulan Ini', $topMedicineCode)
                ->description($topMedicineTotal > 0 ? 'Terjual ' . $topMedicineTotal . ' unit' : '')
                ->color('warning')
                ->chart($medicineChartData),

            Stat::make('Tren Bulanan', 'Transaksi')
                ->description($monthlyCounts[2] > $monthlyCounts[0] ? 'Tren Naik' : 'Tren Turun')
                ->descriptionIcon($monthlyCounts[2] > $monthlyCounts[0] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyCounts[2] > $monthlyCounts[0] ? 'success' : 'danger')
                ->chart($monthlyCounts),
        ];
    }
}
