<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\StockTransferApprovedNotification;
use App\Notifications\StockTransferRejectedNotification;
use App\Notifications\StockTransferRequestNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

    public function sendStockTransferNotification(StockTransfer $stockTransfer)
    {
        $creator = User::find($stockTransfer->created_by);

        if (!$creator) {
            return;
        }

        if ($stockTransfer->isApproved()) {
            $creator->notify(new StockTransferApprovedNotification($stockTransfer));
        } elseif ($stockTransfer->isRejected()) {
            $creator->notify(new StockTransferRejectedNotification($stockTransfer));
        }
    }

    public function sendStockTransferRequestNotification(StockTransfer $stockTransfer)
    {
        // Get all users with Branch Manager role who belong to the same branch
        $branchManagers = User::role('Branch Manager')
            ->where('branch_id', $stockTransfer->from_branch_id)
            ->where('id', '!=', $stockTransfer->created_by)
            ->get();

        // Get all Admin and Super Admin users
        $administrators = User::role(['Admin', 'Super Admin'])->get();

        // Notify branch managers of the same branch
        foreach ($branchManagers as $manager) {
            $manager->notify(new StockTransferRequestNotification($stockTransfer));
        }

        // Notify administrators
        foreach ($administrators as $admin) {
            $admin->notify(new StockTransferRequestNotification($stockTransfer));
        }
    }
}
