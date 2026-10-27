<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToRabPengajuan extends Migration
{
    public function up(): void
    {
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            $table->string('jabatan', 100)->nullable();
            $table->string('bank_nama', 50)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_pemilik_rekening', 100)->nullable();
            $table->decimal('nominal_cash', 15, 2)->default(0);
            $table->decimal('nominal_transfer', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('rab_pengajuan', function (Blueprint $table) {
            $table->dropColumn([
                'jabatan',
                'bank_nama',
                'no_rekening',
                'nama_pemilik_rekening',
                'nominal_cash',
                'nominal_transfer',
            ]);
        });
    }
}