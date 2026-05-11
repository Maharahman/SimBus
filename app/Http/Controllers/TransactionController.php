<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Service;
use App\Models\Inventory;
use App\Services\TransactionService;
use App\Http\Requests\StoreTransactionRequest;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display all transactions with related data.
     */
    public function index()
    {
        $transactions = Transaction::with(['crew', 'details.serviceDetail', 'details.inventoryItem'])
            ->latestFirst()
            ->get();

        $services = Service::all();
        $items = Inventory::where('stock', '>', 0)->get();

        return view('index.children_views.transactions', compact('transactions', 'services', 'items'));
    }

    /**
     * Store a new transaction (Admin+ only).
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $transaction = $this->transactionService->createTransaction(
                $this->currentUser(),
                $request->validated()
            );

            $total = number_format($transaction->total_amount, 0, ',', '.');

            return redirect()->back()->with('success', "Receipt Generated: Rp {$total}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a transaction (Admin+ only).
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->back()->with('success', 'Receipt deleted successfully!');
    }
}