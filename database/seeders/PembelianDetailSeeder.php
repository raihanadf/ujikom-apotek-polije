<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembelianDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembelianDetails = [
            [
                'Nota' => 'NPB001',
                'KdObat' => 'OBT001',
                'Jumlah' => '100',
            ],
            [
                'Nota' => 'NPB002',
                'KdObat' => 'OBT002',
                'Jumlah' => '100',
            ],
            [
                'Nota' => 'NPB003',
                'KdObat' => 'OBT003',
                'Jumlah' => '100',
            ],
        ];

        foreach ($pembelianDetails as $pembelianDetail) {
            DB::table('pembelian_detail')->insert([
                'Nota' => $pembelianDetail['Nota'],
                'KdObat' => $pembelianDetail['KdObat'],
                'Jumlah' => $pembelianDetail['Jumlah'],
            ]);
        }
    }
}
