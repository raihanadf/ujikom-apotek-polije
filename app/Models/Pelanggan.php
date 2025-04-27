<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $primaryKey = 'KdPelanggan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'KdPelanggan',
        'NmPelanggan',
        'Alamat',
        'Kota',
        'Telpon'
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'KdPelanggan', 'KdPelanggan');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'CustomerId', 'KdPelanggan');
    }
}
