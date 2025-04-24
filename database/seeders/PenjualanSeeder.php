<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penjualans = [
            [
                'Nota' => 'NPJ001',
                'TglNota' => '2025-04-24',
                'KdPelanggan' => 'PLG001',
                'Diskon' => '0.00',
            ],
            [
                'Nota' => 'NPJ002',
                'TglNota' => '2025-04-25',
                'KdPelanggan' => 'PLG002',
                'Diskon' => '0.00',
            ],
            [
                'Nota' => 'NPJ003',
                'TglNota' => '2025-04-26',
                'KdPelanggan' => 'PLG003',
                'Diskon' => '0.00',
            ],
            [
                'Nota' => 'NPJ004',
                'TglNota' => '2025-04-27',
                'KdPelanggan' => 'PLG004',
                'Diskon' => '0.00',
            ],
        ];

        foreach ($penjualans as $penjualan) {
            Penjualan::create($penjualan);
        }
    }
}
