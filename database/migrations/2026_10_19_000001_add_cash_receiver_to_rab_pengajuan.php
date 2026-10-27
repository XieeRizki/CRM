<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashReceiverToRabPengajuan extends Migration
{
    public function up(): void
    {
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_pengajuan', 'jabatan')) {
                $table->string('jabatan', 100)->nullable();
            }
            if (!Schema::hasColumn('rab_pengajuan', 'bank_nama')) {
                $table->string('bank_nama', 50)->nullable();
            }
            if (!Schema::hasColumn('rab_pengajuan', 'no_rekening')) {
                $table->string('no_rekening', 50)->nullable();
            }
            if (!Schema::hasColumn('rab_pengajuan', 'nama_pemilik_rekening')) {
                $table->string('nama_pemilik_rekening', 100)->nullable();
            }
            if (!Schema::hasColumn('rab_pengajuan', 'nominal_cash')) {
                $table->decimal('nominal_cash', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('rab_pengajuan', 'nominal_transfer')) {
                $table->decimal('nominal_transfer', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('rab_pengajuan', 'penerima_cash')) {
                $table->string('penerima_cash', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            $table->dropColumn('penerima_cash');
        });
    }
}