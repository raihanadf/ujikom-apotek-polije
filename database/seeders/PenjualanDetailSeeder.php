<?php

namespace Database\Seeders;

use App\Models\PenjulanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penjualanDetails = [
            [
                'Nota' => 'NPJ001',
                'KdObat' => 'OBT001',
                'Jumlah' => '2',
            ],
            [
                'Nota' => 'NPJ002',
                'KdObat' => 'OBT002',
                'Jumlah' => '2',
            ],
            [
                'Nota' => 'NPJ003',
                'KdObat' => 'OBT003',
                'Jumlah' => '2',
            ],
            [
                'Nota' => 'NPJ004',
                'KdObat' => 'OBT004',
                'Jumlah' => '2',
            ],
        ];

        foreach ($penjualanDetails as $penjualanDetail) {
            DB::table('penjualan_detail')->insert([
                'Nota' => $penjualanDetail['Nota'],
                'KdObat' => $penjualanDetail['KdObat'],
                'Jumlah' => $penjualanDetail['Jumlah'],
            ]);
        }
    }
}
