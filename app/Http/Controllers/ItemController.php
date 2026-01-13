<?php
// Item controller: CRUD operations for loanable items.

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::withCount('openLoans')->orderBy('name')->get();
        $editItem = null;

        if ($request->query('edit')) {
            $editItem = Item::find((int) $request->query('edit'));
        }

        return view('items.index', [
            'items' => $items,
            'editItem' => $editItem,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Item::create([
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'serial' => $data['serial'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now_iso(),
        ]);

        return redirect()->route('items.index')->with('success', 'Item added.');
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $item->update([
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'serial' => $data['serial'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('items.index')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $hasLoans = Loan::where('item_id', $item->id)->exists();
        if ($hasLoans) {
            return redirect()->route('items.index')->with('error', 'Cannot delete item with existing loans.');
        }

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }
}
