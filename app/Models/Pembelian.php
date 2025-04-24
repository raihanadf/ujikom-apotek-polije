<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelians';
    protected $primaryKey = 'Nota';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'Nota',
        'TglNota',
        'KdSuplier',
        'Diskon',
    ];

    public function suplier()
    {
        return $this->belongsTo(Suplier::class, 'KdSuplier', 'KdSuplier');
    }

    public function obat()
    {
        return $this->belongsToMany(Obat::class, 'pembelian_detail', 'Nota', 'KdObat')->withPivot('Jumlah');
    }
}
