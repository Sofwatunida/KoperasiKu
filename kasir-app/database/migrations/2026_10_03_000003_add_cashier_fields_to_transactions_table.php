<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'invoice_number' => 'transaction_code',
            'total_price' => 'total',
            'pay_amount' => 'paid',
            'change_amount' => 'change',
        ] as $old => $new) {
            if (Schema::hasColumn('transactions', $old) && ! Schema::hasColumn('transactions', $new)) {
                Schema::table('transactions', fn (Blueprint $table) => $table->renameColumn($old, $new));
            }
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'transaction_code')) {
                $table->string('transaction_code')->nullable();
            }
            if (! Schema::hasColumn('transactions', 'total')) {
                $table->unsignedInteger('total')->default(0);
            }
            if (! Schema::hasColumn('transactions', 'paid')) {
                $table->unsignedInteger('paid')->default(0);
            }
            if (! Schema::hasColumn('transactions', 'change')) {
                $table->unsignedInteger('change')->default(0);
            }
            if (! Schema::hasColumn('transactions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('transactions', 'transaction_date')) {
                $table->dateTime('transaction_date')->nullable();
            }
        });

        DB::table('transactions')->whereNull('transaction_code')->orderBy('id')->get(['id'])->each(function (object $transaction): void {
            DB::table('transactions')->where('id', $transaction->id)->update([
                'transaction_code' => 'TRX-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
            ]);
        });

        DB::table('transactions')->whereNull('transaction_date')->update(['transaction_date' => DB::raw('created_at')]);

        if (! Schema::hasIndex('transactions', 'transactions_transaction_code_unique')) {
            Schema::table('transactions', fn (Blueprint $table) => $table->unique('transaction_code'));
        }

        if (! Schema::hasIndex('transactions', 'transactions_transaction_date_index')) {
            Schema::table('transactions', fn (Blueprint $table) => $table->index('transaction_date'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transactions', 'user_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id', 'transaction_date']);
            });
        }

        foreach ([
            'transaction_code' => 'invoice_number',
            'total' => 'total_price',
            'paid' => 'pay_amount',
            'change' => 'change_amount',
        ] as $current => $old) {
            if (Schema::hasColumn('transactions', $current) && ! Schema::hasColumn('transactions', $old)) {
                Schema::table('transactions', fn (Blueprint $table) => $table->renameColumn($current, $old));
            }
        }
    }
};