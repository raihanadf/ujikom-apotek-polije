<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    use HasFactory;

    protected $table = 'supliers';
    protected $primaryKey = 'KdSuplier';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'KdSuplier',
        'NmSuplier',
        'Alamat',
        'Kota',
        'Telpon'
    ];

    public function obats()
    {
        return $this->hasMany(Obat::class, 'KdSuplier', 'KdSuplier');
    }
}
