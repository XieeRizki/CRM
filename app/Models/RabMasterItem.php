<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabMasterItem extends Model
{
    protected $table = 'rab_master_items';

    protected $fillable = [
        'nama',
        'keterangan',
        'harga',
        'is_active',
        'satuan_unit',
        'kali_per_hari',
        'per_hari',
    ];

    protected $casts = [
        'harga'         => 'float',
        'is_active'     => 'boolean',
        'kali_per_hari' => 'integer',
        'per_hari'      => 'boolean',
    ];
}