<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('products', 'price') && ! Schema::hasColumn('products', 'selling_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('price', 'selling_price');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'code')) {
                $table->string('code', 20)->nullable();
            }
            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('products', 'selling_price')) {
                $table->unsignedInteger('selling_price')->default(0);
            }
            if (! Schema::hasColumn('products', 'purchase_price')) {
                $table->unsignedInteger('purchase_price')->default(0);
            }
            if (! Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0);
            }
            if (! Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 20)->default('pcs');
            }
        });

        DB::table('products')->whereNull('code')->orderBy('id')->get(['id'])->each(function (object $product): void {
            DB::table('products')->where('id', $product->id)->update([
                'code' => 'P'.str_pad((string) $product->id, 3, '0', STR_PAD_LEFT),
            ]);
        });

        DB::table('products')->whereNull('name')->orderBy('id')->get(['id'])->each(function (object $product): void {
            DB::table('products')->where('id', $product->id)->update(['name' => 'Produk '.$product->id]);
        });

        if (! Schema::hasIndex('products', 'products_code_unique')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('code');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(['code', 'purchase_price', 'unit'], fn (string $column) => Schema::hasColumn('products', $column)));

        if ($columns !== []) {
            Schema::table('products', fn (Blueprint $table) => $table->dropColumn($columns));
        }

        if (Schema::hasColumn('products', 'selling_price') && ! Schema::hasColumn('products', 'price')) {
            Schema::table('products', fn (Blueprint $table) => $table->renameColumn('selling_price', 'price'));
        }
    }
};