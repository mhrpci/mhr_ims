<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\StockTransferApprovedNotification;
use App\Notifications\StockTransferRejectedNotification;
use App\Notifications\StockTransferRequestNotification;
use App\Notifications\NearExpiryNotification;
use App\Models\StockIn;

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

        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read');
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

    public function checkAndNotifyNearExpiryStockIns()
    {
        $fifteenDaysFromNow = now()->addDays(15)->endOfDay();
        $user = Auth::user();

        // Get near expiry stock ins
        $query = StockIn::with(['product', 'branch'])
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $fifteenDaysFromNow)
            ->where('expiration_date', '>', now());  // Only get future expiring items

        // Filter by branch if user is branch restricted
        if ($user->isBranchRestricted()) {
            $query->where('branch_id', $user->branch_id);
        }

        $nearExpiryStockIns = $query->get();

        // Get users to notify based on their roles and branches
        foreach ($nearExpiryStockIns as $stockIn) {
            // Get branch managers of the specific branch
            $branchManagers = User::role('Branch Manager')
                ->where('branch_id', $stockIn->branch_id)
                ->get();

            // Get all Admin and Super Admin users
            $administrators = User::role(['Admin', 'Super Admin'])->get();

            // Notify branch managers
            foreach ($branchManagers as $manager) {
                $manager->notify(new NearExpiryNotification($stockIn));
            }

            // Notify administrators
            foreach ($administrators as $admin) {
                $admin->notify(new NearExpiryNotification($stockIn));
            }
        }

        return back()->with('success', 'Near expiry notifications sent successfully.');
    }

}
