<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller
{
    /**
     * Display all inventory items.
     */
    public function index()
    {
        $items = Inventory::with('editor')->latest()->get();

        // Non-admins see read-only view
        if ($this->isAdmin()) {
            return view('index.children_views.inventory', compact('items'));
        }

        return view('index.children_views.inventory_view_only', compact('items'));
    }

    /**
     * Store a new inventory item (Admin+ only).
     */
    public function store(StoreInventoryRequest $request)
    {
        Inventory::create([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'stock' => $request->stock,
            'last_updated_by' => $this->currentUser()->id,
        ]);

        return redirect()->back()->with('success', 'Item added successfully!');
    }

    /**
     * Update an inventory item (Admin+ only).
     */
    public function update(UpdateInventoryRequest $request, $id)
    {
        $item = Inventory::findOrFail($id);
        
        $item->update([
            'item_name' => $request->item_name ?? $item->item_name,
            'item_price' => $request->item_price ?? $item->item_price,
            'stock' => $request->stock ?? $item->stock,
            'last_updated_by' => $this->currentUser()->id,
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    /**
     * Delete an inventory item (Admin+ only).
     */
    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item deleted successfully!');
    }
}