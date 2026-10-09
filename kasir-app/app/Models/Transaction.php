<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'user_id',
        'total',
        'paid',
        'change',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'paid' => 'integer',
            'change' => 'integer',
            'transaction_date' => 'datetime',
        ];
    }

    /**
     * Kasir yang melakukan transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Rincian produk yang dibeli.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function getDateAttribute(): \Illuminate\Support\Carbon
    {
        return $this->transaction_date ?? $this->created_at;
    }

    /**
     * Filter transaksi berdasarkan rentang tanggal.
     */
    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('transaction_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('transaction_date', '<=', $to);
        }

        return $query;
    }
}