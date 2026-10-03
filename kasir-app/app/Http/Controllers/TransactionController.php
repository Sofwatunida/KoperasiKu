<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('details.product')->latest()->get();

        return view('transactions', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('details.product');

        return view('transactions.show', compact('transaction'));
    }
}