<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Eager load everything for the table view
        $transactions = Transaction::with(['crew', 'details.serviceDetail', 'details.inventoryItem'])
            ->orderBy('date', 'desc')
            ->get();

        $services = Service::all();
        $items = Inventory::where('stock', '>', 0)->get();

        return view('index.children_views.transactions', compact('transactions', 'services', 'items'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
        'services' => 'required|array',
        'services.*.id' => 'required|exists:services,id',
        'services.*.qty' => 'required|integer|min:1',
        'items' => 'nullable|array',
        // Check if item id exists in 'inventory' table
        'items.*.id' => 'nullable|exists:inventory,id', 
        'items.*.qty' => 'required_with:items.*.id|integer|min:1',
        ]);

        try {
        DB::beginTransaction();

        // 1. Create the Main Transaction
        // Changed 'modified_by' to 'crew_id' to match your Transaction Model
        $transaction = Transaction::create([
            'date' => now(),
            'modified_by' => Auth::id(), 
            'total_amount' => 0, 
        ]);

        $grandTotal = 0;

        // 2. Process Services
        foreach ($request->services as $s) {
            if (empty($s['id'])) continue; // Skip empty rows

            $serviceObj = Service::find($s['id']);
            $subTotal = $serviceObj->price * $s['qty'];
            
            $transaction->details()->create([
                'item_type' => 'service',
                'item_id' => $s['id'],
                'quantity' => $s['qty'],
                'price_at_time' => $serviceObj->price
            ]);
            $grandTotal += $subTotal;
        }

        // 3. Process Inventory Items
        if ($request->has('items')) {
            foreach ($request->items as $i) {
                // Ensure the row isn't empty before processing
                if (empty($i['id'])) continue; 

                $itemObj = Inventory::find($i['id']);
                $subTotal = $itemObj->item_price * $i['qty'];

                $transaction->details()->create([
                    'item_type' => 'inventory', // Matches 'inventory' check in Detail Model
                    'item_id' => $i['id'],
                    'quantity' => $i['qty'],
                    'price_at_time' => $itemObj->item_price
                ]);
                $grandTotal += $subTotal;
            }
        }

        // 4. Final Total Update
        $transaction->update(['total_amount' => $grandTotal]);

        DB::commit();
        return redirect()->back()->with('success', 'Receipt Generated: Rp ' . number_format($grandTotal));

        } catch (\Exception $e) {
        DB::rollBack();
        // This will now tell you exactly what column or logic is missing
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
    $transaction = Transaction::findOrFail($id);

    // We loop through each detail to trigger the 'deleting' event in TransactionDetail
    $transaction->details->each(function ($detail) {
        $detail->delete(); 
    });

    // Finally, delete the main receipt
    $transaction->delete();

    return redirect()->back()->with('success', 'Receipt deleted and stock restored!');
    }
}