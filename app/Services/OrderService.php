<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                throw new \RuntimeException('Your cart is empty.');
            }

            // Check stock
            $unavailable = [];
            foreach ($cartItems as $item) {
                if ($item->product->stock_quantity < $item->quantity) {
                    $unavailable[] = $item->product->common_name;
                }
            }

            if (!empty($unavailable)) {
                throw new \RuntimeException(
                    'The following items are no longer available in the requested quantity: '
                    . implode(', ', $unavailable)
                );
            }

            $address = $user->addresses()->findOrFail($data['address_id']);
            $deliveryFee = config('florafetch.delivery_fee');
            $itemsTotal = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);

            $order = Order::create([
                'user_id'                    => $user->id,
                'address_id'                 => $address->id,
                'delivery_address_snapshot'  => $address->toSnapshot(),
                'delivery_date'              => $data['delivery_date'],
                'special_instructions'       => $data['special_instructions'] ?? null,
                'status'                     => 'order_confirmed',
                'delivery_fee'               => $deliveryFee,
                'total_amount'               => $itemsTotal + $deliveryFee,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item->product_id,
                    'product_name_snapshot' => $item->product->common_name,
                    'unit_price_snapshot'   => $item->product->price,
                    'quantity'              => $item->quantity,
                ]);

                // Decrement stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Record initial status history
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'order_confirmed',
                'updated_by' => $user->id,
                'created_at' => now(),
            ]);

            // Clear cart
            $user->cartItems()->delete();

            event(new OrderPlaced($order));

            return $order;
        });
    }

    public function advanceStatus(Order $order, User $admin, ?string $estimatedDeliveryDate = null): Order
    {
        if (!$order->canAdvance()) {
            throw new InvalidStatusTransitionException(
                "Order is already at terminal status: {$order->status_label}"
            );
        }

        $nextStatus = $order->getNextStatus();

        DB::transaction(function () use ($order, $admin, $nextStatus, $estimatedDeliveryDate) {
            $updates = ['status' => $nextStatus];

            if ($nextStatus === 'delivered') {
                $updates['delivered_at'] = now();
            }

            if ($nextStatus === 'in_transit' && $estimatedDeliveryDate) {
                $updates['estimated_delivery_date'] = $estimatedDeliveryDate;
            }

            $order->update($updates);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => $nextStatus,
                'updated_by' => $admin->id,
                'created_at' => now(),
            ]);
        });

        event(new OrderStatusUpdated($order->fresh()));

        return $order->fresh();
    }

    public function markRefused(Order $order, User $admin): Order
    {
        $order->update([
            'status'     => 'delivery_refused',
            'refused_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => 'delivery_refused',
            'updated_by' => $admin->id,
            'created_at' => now(),
        ]);

        return $order->fresh();
    }
}
