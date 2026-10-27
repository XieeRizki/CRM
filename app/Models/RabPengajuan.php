<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RabPengajuan extends Model
{
    use SoftDeletes;

    protected $table = 'rab_pengajuan';

    protected $fillable = [
        'kode_rab',
        'kode_transaksi',
        'user_id',
        'nama_pemohon',
        'jabatan',
        'telepon',
        'email',
        'tanggal_pengajuan',
        'lokasi_tujuan',
        'keperluan',
        'jumlah_hari',
        'tanggal_berangkat',
        'tanggal_pulang',
        'metode_pembayaran',
        'bank_nama',
        'no_rekening',
        'nama_pemilik_rekening',
        'penerima_cash',
        'status',
        'total_anggaran',
        'nominal_cash',
        'nominal_transfer',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_berangkat' => 'datetime',
        'tanggal_pulang'    => 'datetime',
        'jumlah_hari'       => 'integer',
        'total_anggaran'    => 'float',
        'nominal_cash'      => 'float',
        'nominal_transfer'  => 'float',
    ];

    public function items()
    {
        return $this->hasMany(RabPengajuanItem::class, 'rab_pengajuan_id', 'id')->orderBy('urutan');
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /** Angka -> kata, contoh: 5100000 -> "lima juta seratus ribu rupiah" */
    public static function terbilang($angka): string
    {
        $n = (int) round(abs($angka));
        if ($n === 0) {
            return 'nol rupiah';
        }

        $s = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        $t = function ($x) use (&$t, $s) {
            if ($x < 12)            return $s[$x];
            if ($x < 20)            return $t($x - 10) . ' belas';
            if ($x < 100)           return $t(intdiv($x, 10)) . ' puluh' . ($x % 10 ? ' ' . $t($x % 10) : '');
            if ($x < 200)           return 'seratus' . ($x - 100 ? ' ' . $t($x - 100) : '');
            if ($x < 1000)          return $t(intdiv($x, 100)) . ' ratus' . ($x % 100 ? ' ' . $t($x % 100) : '');
            if ($x < 2000)          return 'seribu' . ($x - 1000 ? ' ' . $t($x - 1000) : '');
            if ($x < 1000000)       return $t(intdiv($x, 1000)) . ' ribu' . ($x % 1000 ? ' ' . $t($x % 1000) : '');
            if ($x < 1000000000)    return $t(intdiv($x, 1000000)) . ' juta' . ($x % 1000000 ? ' ' . $t($x % 1000000) : '');
            if ($x < 1000000000000) return $t(intdiv($x, 1000000000)) . ' miliar' . ($x % 1000000000 ? ' ' . $t($x % 1000000000) : '');
            return $t(intdiv($x, 1000000000000)) . ' triliun' . ($x % 1000000000000 ? ' ' . $t($x % 1000000000000) : '');
        };

        return trim($t($n)) . ' rupiah';
    }
}