<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = $request->string('search')->trim()->toString();

        $transactions = Transaction::query()
            ->with('user')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('transaction_code', 'like', '%'.$keyword.'%');
            })
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'search' => $keyword,
        ]);
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['details.product', 'user']);

        return view('transactions.show', compact('transaction'));
    }
}