<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembelians = [
            [
                'Nota' => 'NPB001',
                'TglNota' => '2025-04-24',
                'KdSuplier' => 'SUP001',
                'Diskon' => '0.00',
            ],
            [
                'Nota' => 'NPB002',
                'TglNota' => '2025-04-25',
                'KdSuplier' => 'SUP002',
                'Diskon' => '0.00',
            ],
            [
                'Nota' => 'NPB003',
                'TglNota' => '2025-04-26',
                'KdSuplier' => 'SUP003',
                'Diskon' => '0.00',
            ],
        ];

        foreach ($pembelians as $pembelian) {
            Pembelian::create($pembelian);
        }
    }
}
