<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Router;
use App\Services\Session;
use App\Services\Auth;

class OrderController
{
    public function index(): void
    {
        $orders = Order::all();
        Router::view('orders/index', ['orders' => $orders]);
    }

    public function show(int $id): void
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $id);
        Router::view('orders/show', ['order' => $order, 'items' => $orderItems]);
    }

    public function store(): void
    {
        $user = Auth::user();
        
        $orderNumber = 'AFT-' . strtoupper(uniqid());
        
        $order = Order::create([
            'buyer_id' => $user->id,
            'order_number' => $orderNumber,
            'total' => $_POST['total'] ?? 0,
            'status' => 'pending',
            'shipping_address' => $_POST['shipping_address'] ?? '',
            'payment_status' => 'pending',
        ]);

        $cartItems = $_POST['items'] ?? [];
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        Session::flash('success', 'Order placed successfully');
        Router::redirect('/orders');
    }

    public function update(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->update($_POST);
        Session::flash('success', 'Order updated successfully');
        Router::redirect('/orders');
    }
}
