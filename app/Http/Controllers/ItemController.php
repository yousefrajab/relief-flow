<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Item::create($request->only(['name', 'category', 'unit', 'description']));

        return redirect()->route('dashboard')->with('success', __('New relief item has been added successfully.'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $item->update($request->only(['name', 'category', 'unit', 'description']));

        return redirect()->route('dashboard')->with('success', __('Relief item has been updated successfully.'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->inventories()->where('quantity', '>', 0)->exists()) {
            return redirect()->route('dashboard')->with('error', __('This item still has stock in a warehouse and cannot be deleted.'));
        }

        $item->delete();

        return redirect()->route('dashboard')->with('success', __('Relief item has been deleted successfully.'));
    }
}
