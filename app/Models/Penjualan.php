<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';
    protected $primaryKey = 'Nota';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'Nota',
        'TglNota',
        'KdPelanggan',
        'Diskon',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'KdPelanggan', 'KdPelanggan');
    }

    public function obat()
    {
        return $this->belongsToMany(Obat::class, 'penjualan_detail', 'Nota', 'KdObat')->withPivot('Jumlah');
    }
}
