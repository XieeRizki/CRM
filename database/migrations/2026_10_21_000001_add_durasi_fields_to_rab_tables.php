<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDurasiFieldsToRabTables extends Migration
{
    public function up(): void
    {
        // Katalog: satuan unit, default kali per hari, dan apakah dikali durasi
        Schema::table('rab_master_items', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_master_items', 'satuan_unit')) {
                $table->string('satuan_unit', 30)->nullable();
            }
            if (!Schema::hasColumn('rab_master_items', 'kali_per_hari')) {
                $table->unsignedSmallInteger('kali_per_hari')->default(1);
            }
            if (!Schema::hasColumn('rab_master_items', 'per_hari')) {
                $table->boolean('per_hari')->default(true);
            }
        });

        // Pengajuan: tanggal pulang
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_pengajuan', 'tanggal_pulang')) {
                $table->timestamp('tanggal_pulang')->nullable();
            }
        });

        // Item pengajuan: rincian perhitungan (qty = jumlah_unit x kali_per_hari x durasi_hari)
        Schema::table('rab_pengajuan_items', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_pengajuan_items', 'jumlah_unit')) {
                $table->integer('jumlah_unit')->default(1);
            }
            if (!Schema::hasColumn('rab_pengajuan_items', 'kali_per_hari')) {
                $table->unsignedSmallInteger('kali_per_hari')->default(1);
            }
            if (!Schema::hasColumn('rab_pengajuan_items', 'durasi_hari')) {
                $table->unsignedSmallInteger('durasi_hari')->default(1);
            }
            if (!Schema::hasColumn('rab_pengajuan_items', 'satuan_unit')) {
                $table->string('satuan_unit', 30)->nullable();
            }
        });

        // Isi pengaturan untuk 3 item katalog yang sudah ada
        DB::table('rab_master_items')->where('nama', 'Transportasi - Mobil')
            ->update(['satuan_unit' => 'mobil', 'kali_per_hari' => 1, 'per_hari' => true]);

        DB::table('rab_master_items')->where('nama', 'Akomodasi - Makan & Minum')
            ->update(['satuan_unit' => 'orang', 'kali_per_hari' => 3, 'per_hari' => true, 'keterangan' => 'per orang / kali makan']);

        DB::table('rab_master_items')->where('nama', 'Penginapan - Hotel')
            ->update(['satuan_unit' => 'kamar', 'kali_per_hari' => 1, 'per_hari' => true]);
    }

    public function down(): void
    {
        Schema::table('rab_pengajuan_items', function (Blueprint $table) {
            $table->dropColumn(['jumlah_unit', 'kali_per_hari', 'durasi_hari', 'satuan_unit']);
        });
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            $table->dropColumn('tanggal_pulang');
        });
        Schema::table('rab_master_items', function (Blueprint $table) {
            $table->dropColumn(['satuan_unit', 'kali_per_hari', 'per_hari']);
        });
    }
}