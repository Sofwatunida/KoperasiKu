<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['invoice_number', 'total_price', 'pay_amount', 'change_amount', 'items'];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }
}
