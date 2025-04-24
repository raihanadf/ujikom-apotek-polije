<?php

namespace Database\Seeders;

use App\Models\Suplier;
use Illuminate\Database\Seeder;

class SuplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supliers = [
            [
                'KdSuplier' => 'SUP001',
                'NmSuplier' => 'PT Pharma Indonesia',
                'Alamat' => 'Jl. Industri No. 123',
                'Kota' => 'Jakarta',
                'Telpon' => '021-5551234',
            ],
            [
                'KdSuplier' => 'SUP002',
                'NmSuplier' => 'CV Medika Jaya',
                'Alamat' => 'Jl. Kesehatan No. 45',
                'Kota' => 'Bandung',
                'Telpon' => '022-4567890',
            ],
            [
                'KdSuplier' => 'SUP003',
                'NmSuplier' => 'PT Sehat Sentosa',
                'Alamat' => 'Jl. Apoteker No. 78',
                'Kota' => 'Surabaya',
                'Telpon' => '031-7890123',
            ],
        ];

        foreach ($supliers as $suplier) {
            Suplier::create($suplier);
        }
    }
}
