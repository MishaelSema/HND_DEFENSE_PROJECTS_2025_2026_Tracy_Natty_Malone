<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Router;
use App\Services\Session;
use App\Services\Auth;

class CheckoutController
{
    public function index(): void
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            Session::flash('error', 'Your cart is empty');
            Router::redirect('/AFROTOUCH/public/products');
            return;
        }
        
        $items = [];
        $total = 0;
        
        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty
                ];
                $total += $product->price * $qty;
            }
        }
        
        Router::view('checkout/index', [
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function checkoutForm(): void
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            Session::flash('error', 'Your cart is empty');
            Router::redirect('/AFROTOUCH/public/products');
            return;
        }
        
        $items = [];
        $total = 0;
        
        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty
                ];
                $total += $product->price * $qty;
            }
        }
        
        Router::view('checkout/form', [
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function process(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to checkout');
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            Router::redirect('/AFROTOUCH/public/products');
            return;
        }
        
        $user = Auth::user();
        $items = [];
        $total = 0;
        
        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product && $product->status === 'active') {
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty
                ];
                $total += $product->price * $qty;
            }
        }
        
        if (empty($items)) {
            Session::flash('error', 'No valid items in cart');
            Router::redirect('/AFROTOUCH/public/cart');
            return;
        }
        
        $orderNumber = 'AFT-' . rand(1000, 9999);
        
        $order = Order::create([
            'buyer_id' => $user->id,
            'buyer_name' => $user->name,
            'buyer_email' => $user->email,
            'buyer_phone' => $user->phone,
            'order_number' => $orderNumber,
            'total' => $total,
            'status' => 'pending',
            'shipping_address' => $_POST['address'] ?? '',
            'payment_status' => 'pending',
            'payment_method' => $_POST['payment_method'] ?? 'cash',
        ]);
        
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'price' => $item['product']->price,
            ]);
        }
        
        Session::forget('cart');
        
        Router::redirect('/AFROTOUCH/public/checkout/success?order=' . $orderNumber);
    }
    
    public function success(): void
    {
        $orderNumber = $_GET['order'] ?? '';
        
        Router::view('checkout/success', [
            'order_number' => $orderNumber
        ]);
    }
    
    public function addToCart(int $id): void
    {
        $product = Product::find($id);
        
        if (!$product || $product->status !== 'active') {
            Session::flash('error', 'Product not available');
            Router::redirect('/AFROTOUCH/public/products');
            return;
        }
        
        $cart = Session::get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        Session::set('cart', $cart);
        
        Session::flash('success', 'Added to cart');
        Router::redirect('/AFROTOUCH/public/products/' . $id);
    }
    
    public function updateCart(): void
    {
        $productId = $_POST['product_id'] ?? 0;
        $change = $_POST['change'] ?? 0;
        
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $change;
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
        }
        
        Session::set('cart', $cart);
        echo json_encode(['success' => true]);
    }
    
    public function removeFromCart(): void
    {
        $productId = $_POST['product_id'] ?? 0;
        
        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::set('cart', $cart);
        
        echo json_encode(['success' => true]);
    }
}
