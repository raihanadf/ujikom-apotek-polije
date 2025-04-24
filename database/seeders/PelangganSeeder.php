<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'KdPelanggan' => 'PLG001',
                'NmPelanggan' => 'Rumah Sakit Medika',
                'Alamat' => 'Jl. Pahlawan No. 10',
                'Kota' => 'Jakarta',
                'Telpon' => '021-9876543',
            ],
            [
                'KdPelanggan' => 'PLG002',
                'NmPelanggan' => 'Klinik Sehat',
                'Alamat' => 'Jl. Sudirman No. 25',
                'Kota' => 'Bandung',
                'Telpon' => '022-8765432',
            ],
            [
                'KdPelanggan' => 'PLG003',
                'NmPelanggan' => 'Apotek Sejahtera',
                'Alamat' => 'Jl. Raya Utama No. 55',
                'Kota' => 'Surabaya',
                'Telpon' => '031-7654321',
            ],
            [
                'KdPelanggan' => 'PLG004',
                'NmPelanggan' => 'Dr. Budi Santoso',
                'Alamat' => 'Jl. Menteng No. 15',
                'Kota' => 'Jakarta',
                'Telpon' => '021-5432109',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
