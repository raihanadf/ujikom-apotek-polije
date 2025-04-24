<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obats';
    protected $primaryKey = 'KdObat';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'KdObat',
        'NmObat',
        'Jenis',
        'Satuan',
        'HargaBeli',
        'HargaJual',
        'Stok',
        'KdSuplier'
    ];

    public function suplier()
    {
        return $this->belongsTo(Suplier::class, 'KdSuplier', 'KdSuplier');
    }
}
