<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isBranchRestricted()) {
            $stockOuts = StockOut::with(['product', 'customer', 'branch'])
                ->where('branch_id', $user->branch_id)
                ->get();
        } else {
            $stockOuts = StockOut::with(['product', 'customer', 'branch'])->get();
        }

        return view('stock_outs.index', compact('stockOuts'));
    }

    public function create()
    {
        $user = Auth::user();
        $products = Product::all();
        $customers = Customer::all();

        if ($user->isBranchRestricted()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('stock_outs.create', compact('products', 'customers', 'branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'nullable',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only remove stock from your assigned branch.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;

        $inventory = Inventory::where('product_id', $validatedData['product_id'])
            ->where('branch_id', $validatedData['branch_id'])
            ->first();

        if (!$inventory || $inventory->quantity < $validatedData['quantity']) {
            return redirect()->back()->withInput()
                ->with('error', 'Insufficient stock for this product in the selected branch.');
        }

        DB::transaction(function () use ($validatedData, $inventory) {
            StockOut::create($validatedData);

            $inventory->quantity -= $validatedData['quantity'];
            $inventory->save();
        });

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('stock_outs.create')
                ->with('success', 'Stock Out recorded successfully. Create another one.');
        }

        return redirect()->route('stock_outs.index')
            ->with('success', 'Stock Out recorded successfully.');
    }

    public function show(StockOut $stockOut)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $stockOut->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this stock out record.');
        }

        return view('stock_outs.show', compact('stockOut'));
    }
}
