<?php

namespace App\Http\Controllers;

use App\Models\ForPhss;
use App\Models\Product;
use Illuminate\Http\Request;

class ForPhssController extends Controller
{
    public function index()
    {
        $forPhsses = ForPhss::with('product')
            ->whereHas('product', function ($query) {
                $query->where('branch_id', auth()->user()->branch_id);
            })
            ->latest()
            ->paginate(10);

        return view('for-phss.index', compact('forPhsses'));
    }

    public function create()
    {
        if (auth()->user()->branch_id) {
            // User has branch_id - fetch products from their branch only
            $products = Product::where('branch_id', auth()->user()->branch_id)->get();
        } else {
            // User has no branch_id - fetch all products with branch information
            $products = Product::with('branch')->get();
        }

        return view('for-phss.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'phss_id' => 'required',
            'hospital_id' => 'required',
            'status' => 'required|in:for_demo,for_evaluation,returned',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            // Find the product
            $product = Product::findOrFail($validated['product_id']);
            
            // Check branch access
            if (auth()->user()->branch_id && $product->branch_id !== auth()->user()->branch_id) {
                return back()
                    ->with('error', 'You are not authorized to use products from other branches')
                    ->withInput();
            }

            // Get inventory for the branch
            $inventory = $product->inventories()
                ->where('branch_id', auth()->user()->branch_id)
                ->firstOrFail();

            // Check inventory quantity for non-returned status
            if (in_array($validated['status'], ['for_demo', 'for_evaluation'])) {
                if ($inventory->quantity < $validated['qty']) {
                    return back()
                        ->with('error', 'Insufficient inventory quantity')
                        ->withInput();
                }
                
                // Deduct inventory
                $inventory->quantity -= $validated['qty'];
                $inventory->save();
            }

            // Create the PHSS record
            $forPhss = ForPhss::create([
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'phss_id' => $validated['phss_id'],
                'hospital_id' => $validated['hospital_id'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
                'inventory_id' => $inventory->id,
            ]);

            // If status is 'returned', add quantity back to inventory
            if ($validated['status'] === 'returned') {
                $inventory->quantity += $validated['qty'];
                $inventory->save();
            }

            return redirect()
                ->route('for-phss.index')
                ->with('success', 'PHSS record created successfully');

        } catch (\Exception $e) {
            \Log::error('PHSS Creation Error: ' . $e->getMessage());
            return back()
                ->with('error', 'An error occurred while creating the record. Please try again.')
                ->withInput();
        }
    }

    public function edit(ForPhss $forPhss)
    {
        if ($forPhss->product->branch_id !== auth()->user()->branch_id) {
            return back()->with('error', 'Unauthorized access');
        }

        $products = Product::where('branch_id', auth()->user()->branch_id)->get();
        return view('for-phss.edit', compact('forPhss', 'products'));
    }

    public function update(Request $request, ForPhss $forPhss)
    {
        if ($forPhss->product->branch_id !== auth()->user()->branch_id) {
            return back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'status' => 'required|in:for_demo,for_evaluation,returned',
            'note' => 'nullable|string',
        ]);

        try {
            // Get the inventory
            $inventory = $forPhss->inventory;

            // Handle inventory updates based on status change
            if ($forPhss->status !== $validated['status']) {
                // If changing from for_demo/for_evaluation to returned
                if (in_array($forPhss->status, ['for_demo', 'for_evaluation']) && 
                    $validated['status'] === 'returned') {
                    $inventory->quantity += $forPhss->qty;
                    $inventory->save();
                }
                // If changing from returned to for_demo/for_evaluation
                elseif ($forPhss->status === 'returned' && 
                        in_array($validated['status'], ['for_demo', 'for_evaluation'])) {
                    if ($inventory->quantity < $forPhss->qty) {
                        return back()
                            ->with('error', 'Insufficient inventory quantity')
                            ->withInput();
                    }
                    $inventory->quantity -= $forPhss->qty;
                    $inventory->save();
                }
            }

            $forPhss->update($validated);
            return redirect()
                ->route('for-phss.index')
                ->with('success', 'Record updated successfully');

        } catch (\Exception $e) {
            \Log::error('PHSS Update Error: ' . $e->getMessage());
            return back()
                ->with('error', 'An error occurred while updating the record. Please try again.')
                ->withInput();
        }
    }

    public function destroy(ForPhss $forPhss)
    {
        if ($forPhss->product->branch_id !== auth()->user()->branch_id) {
            return back()->with('error', 'Unauthorized access');
        }

        $forPhss->delete();
        return redirect()->route('for-phss.index')->with('success', 'Record deleted successfully');
    }

    public function show(ForPhss $forPhss)
    {
        if ($forPhss->product->branch_id !== auth()->user()->branch_id) {
            return back()->with('error', 'Unauthorized access');
        }

        return view('for-phss.show', compact('forPhss'));
    }
} 