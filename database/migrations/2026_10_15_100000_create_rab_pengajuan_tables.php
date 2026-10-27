<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRabPengajuanTables extends Migration
{
    public function up(): void
    {
        // 1) Katalog item biaya (yang muncul di kotak "Klik untuk pilih atau cari item...")
        Schema::create('rab_master_items', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('keterangan')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2) Pengajuan RAB (header)
        Schema::create('rab_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rab', 50)->unique();
            $table->bigInteger('user_id')->nullable();
            $table->string('nama_pemohon');
            $table->string('telepon', 50)->nullable();
            $table->string('email')->nullable();
            $table->timestamp('tanggal_pengajuan');
            $table->string('lokasi_tujuan');
            $table->text('keperluan');
            $table->unsignedSmallInteger('jumlah_hari')->default(1);
            $table->timestamp('tanggal_berangkat')->nullable();
            $table->string('metode_pembayaran', 20)->default('Transfer'); // Transfer, Cash, Split
            $table->string('status', 20)->default('Menunggu');
            $table->decimal('total_anggaran', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });

        // 3) Item biaya per pengajuan (nama & harga disalin supaya riwayat tidak berubah kalau katalog diubah)
        Schema::create('rab_pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rab_pengajuan_id');
            $table->unsignedBigInteger('master_item_id')->nullable();
            $table->string('nama_item');
            $table->string('keterangan')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('rab_pengajuan_id')->references('id')->on('rab_pengajuan')->onDelete('cascade');
            $table->foreign('master_item_id')->references('id')->on('rab_master_items')->onDelete('set null');
        });

        // Data awal katalog (sesuai contoh di desain)
        $now = now();
        DB::table('rab_master_items')->insert([
            ['nama' => 'Transportasi - Mobil',      'keterangan' => '2 tarif per mobil / hari', 'harga' => 300000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Akomodasi - Makan & Minum', 'keterangan' => 'per orang 3 pax / hari',   'harga' => 200000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Penginapan - Hotel',        'keterangan' => 'per kamar / hari',         'harga' => 500000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_pengajuan_items');
        Schema::dropIfExists('rab_pengajuan');
        Schema::dropIfExists('rab_master_items');
    }
}