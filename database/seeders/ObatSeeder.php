<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obats = [
            [
                'KdObat' => 'OBT001',
                'NmObat' => 'Paracetamol 500mg',
                'Jenis' => 'Tablet',
                'Satuan' => 'Strip',
                'HargaBeli' => 8500.00,
                'HargaJual' => 10000.00,
                'Stok' => 100,
                'KdSuplier' => 'SUP001',
            ],
            [
                'KdObat' => 'OBT002',
                'NmObat' => 'Amoxicillin 500mg',
                'Jenis' => 'Kapsul',
                'Satuan' => 'Strip',
                'HargaBeli' => 15000.00,
                'HargaJual' => 18000.00,
                'Stok' => 75,
                'KdSuplier' => 'SUP001',
            ],
            [
                'KdObat' => 'OBT003',
                'NmObat' => 'Vitamin C 1000mg',
                'Jenis' => 'Tablet',
                'Satuan' => 'Botol',
                'HargaBeli' => 45000.00,
                'HargaJual' => 55000.00,
                'Stok' => 50,
                'KdSuplier' => 'SUP002',
            ],
            [
                'KdObat' => 'OBT004',
                'NmObat' => 'Antasida',
                'Jenis' => 'Sirup',
                'Satuan' => 'Botol',
                'HargaBeli' => 12000.00,
                'HargaJual' => 16500.00,
                'Stok' => 40,
                'KdSuplier' => 'SUP002',
            ],
            [
                'KdObat' => 'OBT005',
                'NmObat' => 'Dexamethasone 0.5mg',
                'Jenis' => 'Tablet',
                'Satuan' => 'Strip',
                'HargaBeli' => 9000.00,
                'HargaJual' => 12000.00,
                'Stok' => 60,
                'KdSuplier' => 'SUP003',
            ],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }
    }
}
