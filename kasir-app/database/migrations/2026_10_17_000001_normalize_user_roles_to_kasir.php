<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selaraskan role lama "petugas" menjadi "kasir" dan ubah nilai default kolom role.
     *
     * Kolom users.role sudah tersedia sejak migrasi 2026_10_03_000001 sehingga
     * tidak ada tabel/kolom baru yang dibuat, hanya penyesuaian nilai role.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')->where('role', 'petugas')->update(['role' => 'kasir']);
        DB::table('users')->whereNull('role')->orWhere('role', '')->update(['role' => 'kasir']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('kasir')->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')->where('role', 'kasir')->update(['role' => 'petugas']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('petugas')->change();
        });
    }
};
