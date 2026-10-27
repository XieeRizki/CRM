<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddKodeTransaksiToRabPengajuan extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rab_pengajuan', 'kode_transaksi')) {
            Schema::table('rab_pengajuan', function (Blueprint $table) {
                $table->string('kode_transaksi', 30)->nullable();
            });
        }

        // Isi kode untuk RAB yang sudah ada, urut sesuai ID: TRX-001, TRX-002, dst.
        $nomor = 0;
        $rows = DB::table('rab_pengajuan')->whereNull('kode_transaksi')->orderBy('id')->get(['id']);
        foreach ($rows as $row) {
            $nomor++;
            DB::table('rab_pengajuan')->where('id', $row->id)
                ->update(['kode_transaksi' => 'TRX-' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT)]);
        }

        if (!Schema::hasIndex('rab_pengajuan', 'rab_pengajuan_kode_transaksi_unique')) {
            Schema::table('rab_pengajuan', function (Blueprint $table) {
                $table->unique('kode_transaksi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            $table->dropUnique(['kode_transaksi']);
            $table->dropColumn('kode_transaksi');
        });
    }
}