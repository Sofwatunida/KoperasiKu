<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * Batas stok menipis. Stok <= nilai ini berstatus "rendah".
     */
    public const LOW_STOCK_THRESHOLD = 10;

    protected $fillable = [
        'code',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'unit',
    ];

    /**
     * Satu produk bisa muncul di banyak detail transaksi.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    protected function casts(): array
    {
        return [
            'purchase_price' => 'integer',
            'selling_price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function getStockStatusAttribute(): string
    {
        return match (true) {
            $this->stock <= 0 => 'habis',
            $this->stock <= self::LOW_STOCK_THRESHOLD => 'rendah',
            default => 'normal',
        };
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'Stok habis',
            'rendah' => 'Stok rendah',
            default => 'Stok normal',
        };
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Pencarian produk berdasarkan kode atau nama produk.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('code', 'like', '%'.$keyword.'%')
                ->orWhere('name', 'like', '%'.$keyword.'%');
        });
    }
}