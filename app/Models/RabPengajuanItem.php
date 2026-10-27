<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabPengajuanItem extends Model
{
    protected $table = 'rab_pengajuan_items';

    protected $fillable = [
        'rab_pengajuan_id',
        'master_item_id',
        'nama_item',
        'keterangan',
        'qty',
        'harga',
        'jumlah',
        'urutan',
    ];

    protected $casts = [
        'qty'    => 'integer',
        'harga'  => 'float',
        'jumlah' => 'float',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(RabPengajuan::class, 'rab_pengajuan_id', 'id');
    }

    public function masterItem()
    {
        return $this->belongsTo(RabMasterItem::class, 'master_item_id', 'id');
    }
}