<?php
// app/Http/Controllers/InventoryController.php
namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $items = Inventory::with('editor')->get();        
        if (($auth->level === 'developer') || ($auth->level === 'admin')) {
            // Full feature for dev and admin
            return view('index.children_views.inventory', compact('items'));
        } else {
            // User only see stock
            return view('index.children_views.inventory_view_only', compact('items'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
        ]);

        Inventory::create([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'stock' => $request->stock,
            'last_updated_by' => Auth::id(), // The "By Who"
        ]);

        return redirect()->back()->with('success', 'Item Added.');
    }

    public function update(Request $request, $inventory) // Change $id to $inventory
    {
        $item = Inventory::findOrFail($inventory);
        $item->update([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'stock' => $request->stock,
            'last_updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Item Updated.');
    }

    public function destroy($inventory) // Change $id to $inventory
    {
        Inventory::findOrFail($inventory)->delete();
        return redirect()->back()->with('success', 'Item Deleted.');
    }
}