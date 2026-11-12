<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Router;
use App\Services\Auth;

class OrderController
{
    public function myOrders(): void
    {
        if (!Auth::check()) {
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $user = Auth::user();
        $orders = Order::findByBuyer($user->id);
        
        Router::view('orders/my-orders', [
            'orders' => $orders,
            'user' => $user
        ]);
    }
    
    public function show(int $id): void
    {
        if (!Auth::check()) {
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $user = Auth::user();
        $order = Order::find($id);
        
        if (!$order || $order->buyer_id != $user->id) {
            Session::flash('error', 'Order not found');
            Router::redirect('/AFROTOUCH/public/orders');
            return;
        }
        
        $items = OrderItem::findByOrder($order->id);
        
        Router::view('orders/detail', [
            'order' => $order,
            'items' => $items
        ]);
    }
}
