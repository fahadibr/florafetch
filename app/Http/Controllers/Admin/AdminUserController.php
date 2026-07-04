<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function deactivate(User $user)
    {
        $results = ['session' => 'ok', 'orders' => 'ok', 'failed_orders' => []];

        // Deactivate account
        $user->update(['is_active' => false]);

        // Invalidate sessions by deleting from sessions table
        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Exception $e) {
            $results['session'] = 'failed: ' . $e->getMessage();
        }

        // Cancel eligible orders
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['order_confirmed', 'quality_check'])
            ->get();

        foreach ($orders as $order) {
            try {
                $order->update(['status' => 'delivery_refused']);
            } catch (\Exception $e) {
                $results['failed_orders'][] = $order->id;
            }
        }

        if (!empty($results['failed_orders'])) {
            $results['orders'] = 'Some orders could not be cancelled: #' . implode(', #', $results['failed_orders']);
        }

        $summary = "Account deactivated. Session: {$results['session']}. Orders: {$results['orders']}.";

        return back()->with('success', $summary);
    }
}
