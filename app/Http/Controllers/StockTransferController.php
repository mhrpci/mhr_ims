<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Inventory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\StockTransferRequested;
use App\Notifications\StockTransferRequestNotification;
use App\Models\User;
use App\Events\StockTransferEvent;
use App\Notifications\StockTransferApprovedNotification;
use App\Notifications\StockTransferRejectedNotification;

class StockTransferController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isBranchRestricted()) {
            $stockTransfers = StockTransfer::with([
                'inventory.product',
                'fromBranch',
                'toBranch',
                'createdBy',
                'approvedBy'
            ])
                ->where('from_branch_id', $user->branch_id)
                ->orWhere('to_branch_id', $user->branch_id)
                ->get();
        } else {
            $stockTransfers = StockTransfer::with([
                'inventory.product',
                'fromBranch',
                'toBranch',
                'createdBy',
                'approvedBy'
            ])->get();
        }

        return view('stock_transfers.index', compact('stockTransfers'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->isBranchRestricted()) {
            $fromBranch = Branch::find($user->branch_id);
            $inventories = Inventory::with('product')
                ->where('branch_id', $user->branch_id)
                ->where('quantity', '>', 0)
                ->get();
            $toBranches = Branch::where('id', '!=', $user->branch_id)->get();
            $branches = Branch::all();
        } else {
            $fromBranch = null;
            $inventories = Inventory::with('product')
                ->where('quantity', '>', 0)
                ->get();
            $toBranches = Branch::all();
            $branches = Branch::all();
        }

        return view('stock_transfers.create', compact(
            'inventories',
            'toBranches',
            'fromBranch',
            'branches'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'from_branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    // Stock Manager can only transfer from their branch
                    if ($user->hasRole('Stock Manager') && $value != $user->branch_id) {
                        $fail('You can only transfer stock from your assigned branch.');
                    }
                    // Branch Manager can only transfer from their branch
                    if ($user->hasRole('Branch Manager') && $value != $user->branch_id) {
                        $fail('You can only transfer stock from your assigned branch.');
                    }
                },
            ],
            'to_branch_id' => [
                'required',
                'exists:branches,id',
                'different:from_branch_id',
            ],
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::findOrFail($validatedData['inventory_id']);

        // Verify inventory belongs to user's branch for Stock Manager
        if ($user->hasRole('Stock Manager') && $inventory->branch_id !== $user->branch_id) {
            return back()->withErrors(['inventory_id' => 'You can only transfer inventory from your assigned branch.']);
        }

        if ($inventory->quantity < $validatedData['quantity']) {
            return back()->withErrors(['quantity' => 'Transfer quantity cannot exceed available inventory.']);
        }

        $status = '';

        DB::transaction(function () use ($validatedData, $user, $inventory, &$status) {
            // Initialize additional fields
            $transferData = $validatedData;

            // Set status and approval fields based on role
            if ($user->hasRole(['Admin', 'Super Admin', 'Branch Manager'])) {
                $transferData['status'] = 'approved';
                $status = 'approved';
                $transferData['approved_by'] = $user->id;
                $transferData['approved_at'] = now();

                // Automatically process the transfer for approved status
                // Decrease quantity from source branch
                $inventory->decrement('quantity', $validatedData['quantity']);

                // Increase quantity in destination branch
                $destinationInventory = Inventory::firstOrCreate(
                    [
                        'product_id' => $inventory->product_id,
                        'branch_id' => $validatedData['to_branch_id']
                    ],
                    ['quantity' => 0]
                );

                $destinationInventory->increment('quantity', $validatedData['quantity']);
            } else {
                $transferData['status'] = 'pending';
                $status = 'pending';
            }

            $transferData['created_by'] = $user->id;
            $transferData['updated_by'] = $user->id;

            $stockTransfer = StockTransfer::create($transferData);

            // Broadcast the event
            broadcast(new StockTransferEvent($stockTransfer, 'created'))->toOthers();

            if ($user->hasRole('Stock Manager')) {
                // Notify branch manager
                $branchManager = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Branch Manager');
                })->where('branch_id', $validatedData['from_branch_id'])->first();

                if ($branchManager) {
                    $branchManager->notify(new StockTransferRequestNotification($stockTransfer));
                }

                // Notify admins
                $admins = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Admin', 'Super Admin']);
                })->get();

                foreach ($admins as $admin) {
                    $admin->notify(new StockTransferRequestNotification($stockTransfer));
                }
            }

            // Add notification for auto-approved transfers
            if ($transferData['status'] === 'approved') {
                // Notify relevant users about the completed transfer
                $fromBranchManager = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Branch Manager');
                })->where('branch_id', $validatedData['from_branch_id'])->first();

                $toBranchManager = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Branch Manager');
                })->where('branch_id', $validatedData['to_branch_id'])->first();

                // Notify branch managers if they weren't the one who created the transfer
                if ($fromBranchManager && $fromBranchManager->id !== $user->id) {
                    $fromBranchManager->notify(new StockTransferApprovedNotification($stockTransfer));
                }
                if ($toBranchManager && $toBranchManager->id !== $user->id) {
                    $toBranchManager->notify(new StockTransferApprovedNotification($stockTransfer));
                }
            }
        });

        $successMessage = $status === 'approved'
            ? 'Stock transfer has been created and processed successfully.'
            : 'Stock transfer request has been submitted and is pending approval.';

        return redirect()->route('stock_transfers.index')
            ->with('success', $successMessage);
    }

    public function approve(StockTransfer $stockTransfer)
    {
        $user = Auth::user();

        if (!$user->hasRole(['Admin', 'Super Admin']) &&
            !($user->hasRole('Branch Manager') && $user->branch_id === $stockTransfer->from_branch_id)) {
            abort(403, 'Unauthorized to approve stock transfers.');
        }

        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed.');
        }

        DB::transaction(function () use ($stockTransfer, $user) {
            $inventory = Inventory::findOrFail($stockTransfer->inventory_id);

            if ($inventory->quantity < $stockTransfer->quantity) {
                throw new \Exception('Insufficient inventory quantity.');
            }

            // Decrease quantity from source branch
            $inventory->decrement('quantity', $stockTransfer->quantity);

            // Increase quantity in destination branch
            $destinationInventory = Inventory::firstOrCreate(
                [
                    'product_id' => $inventory->product_id,
                    'branch_id' => $stockTransfer->to_branch_id
                ],
                ['quantity' => 0]
            );

            $destinationInventory->increment('quantity', $stockTransfer->quantity);

            // Update transfer status
            $stockTransfer->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Notify the requester
            $requester = User::find($stockTransfer->created_by);
            $requester->notify(new StockTransferApprovedNotification($stockTransfer));

            // Broadcast the event
            broadcast(new StockTransferEvent($stockTransfer, 'approved'))->toOthers();
        });

        return redirect()->route('stock_transfers.show', $stockTransfer)
            ->with('success', 'Stock transfer has been approved and processed.');
    }

    public function reject(Request $request, StockTransfer $stockTransfer)
    {
        $user = Auth::user();

        if (!$user->hasRole(['Admin', 'Super Admin']) &&
            !($user->hasRole('Branch Manager') && $user->branch_id === $stockTransfer->from_branch_id)) {
            abort(403, 'Unauthorized to reject stock transfers.');
        }

        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $stockTransfer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Notify the requester
        $requester = User::find($stockTransfer->created_by);
        $requester->notify(new StockTransferRejectedNotification($stockTransfer));

        // Broadcast the event
        broadcast(new StockTransferEvent($stockTransfer, 'rejected'))->toOthers();

        return redirect()->route('stock_transfers.show', $stockTransfer)
            ->with('success', 'Stock transfer has been rejected.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() &&
            $stockTransfer->from_branch_id !== $user->branch_id &&
            $stockTransfer->to_branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this stock transfer record.');
        }

        // Eager load all necessary relationships
        $stockTransfer->load([
            'inventory.product',
            'fromBranch',
            'toBranch',
            'createdBy',
            'updatedBy',
            'approvedBy'  // Changed from 'approver' to match the relationship name
        ]);

        return view('stock_transfers.show', compact('stockTransfer'));
    }

    // Edit and Update methods are not included as transfers should be final
    // If needed, create a new transfer to reverse the operation

    public function destroy(StockTransfer $stockTransfer)
    {
        return redirect()->route('stock_transfers.index')
            ->with('error', 'Deleting stock transfers is not allowed.');
    }
}
