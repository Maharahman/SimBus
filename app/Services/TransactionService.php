<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Create a transaction with services and inventory items.
     */
    public function createTransaction(User $user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {
            $transaction = Transaction::create([
                'date' => now(),
                'modified_by' => $user->id,
                'total_amount' => 0,
            ]);

            $grandTotal = 0;

            // Process services
            if (!empty($data['services'])) {
                foreach ($data['services'] as $serviceData) {
                    if (empty($serviceData['id'])) {
                        continue;
                    }

                    $service = Service::findOrFail($serviceData['id']);
                    $quantity = $serviceData['qty'] ?? 1;
                    $subtotal = $service->price * $quantity;

                    $transaction->details()->create([
                        'item_type' => 'service',
                        'item_id' => $service->id,
                        'quantity' => $quantity,
                        'price_at_time' => $service->price,
                    ]);

                    $grandTotal += $subtotal;
                }
            }

            // Process inventory items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    if (empty($itemData['id'])) {
                        continue;
                    }

                    $inventory = Inventory::findOrFail($itemData['id']);
                    $quantity = $itemData['qty'] ?? 1;
                    $subtotal = $inventory->item_price * $quantity;

                    $transaction->details()->create([
                        'item_type' => 'inventory',
                        'item_id' => $inventory->id,
                        'quantity' => $quantity,
                        'price_at_time' => $inventory->item_price,
                    ]);

                    $grandTotal += $subtotal;
                }
            }

            // Update total amount
            $transaction->update(['total_amount' => $grandTotal]);

            return $transaction;
        });
    }

    /**
     * Delete a transaction and its details.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            // Delete all detail records (triggers deleting events if needed)
            $transaction->details()->delete();
            
            // Delete the transaction
            return $transaction->delete();
        });
    }
}