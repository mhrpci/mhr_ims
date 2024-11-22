<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isBranchRestricted()) {
            $inventories = Inventory::with(['product', 'branch'])
                ->where('branch_id', $user->branch_id)
                ->get();
        } else {
            $inventories = Inventory::with(['product', 'branch'])->get();
        }

        return view('inventories.index', compact('inventories'));
    }

    public function create()
    {
        $user = Auth::user();
        $products = Product::all();

        if ($user->isBranchRestricted()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('inventories.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only manage inventory for your assigned branch.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:0',
        ]);

        Inventory::create($validatedData);

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('inventories.create')
                ->with('success', 'Inventory created successfully. Create another one.');
        }

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory created successfully.');
    }

    public function show(Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $inventory->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this inventory record.');
        }

        return view('inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $inventory->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this inventory record.');
        }

        $products = Product::all();

        if ($user->isBranchRestricted()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('inventories.edit', compact('inventory', 'products', 'branches'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $inventory->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this inventory record.');
        }

        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only manage inventory for your assigned branch.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory->update($validatedData);

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $inventory->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this inventory record.');
        }

        $inventory->delete();

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory deleted successfully.');
    }
}
