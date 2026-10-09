<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 50)->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('petugas');
            });
        }

        DB::table('users')->where(function ($query) {
            $query->whereNull('username')->orWhere('username', '');
        })->orderBy('id')->get(['id'])->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update(['username' => 'user'.$user->id]);
        });

        if (! Schema::hasIndex('users', 'users_username_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_username_unique')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropUnique('users_username_unique'));
        }

        $columns = array_values(array_filter(['username', 'role'], fn (string $column) => Schema::hasColumn('users', $column)));

        if ($columns !== []) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};