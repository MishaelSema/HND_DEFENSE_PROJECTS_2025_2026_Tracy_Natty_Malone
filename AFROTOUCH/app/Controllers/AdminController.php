<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\Router;
use App\Services\Auth;

class AdminController
{
    private function checkAdmin(): void
    {
        if (!Auth::check()) {
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $user = Auth::user();
        if ($user->role !== 'admin') {
            Router::redirect('/AFROTOUCH/public/');
            return;
        }
    }
    
    public function index(): void
    {
        $this->checkAdmin();
        
        $stats = [
            'total_users' => User::count(),
            'total_sellers' => User::countByRole('seller'),
            'total_buyers' => User::countByRole('buyer'),
            'total_products' => Product::countAll(),
            'total_orders' => Order::countAll(),
            'total_revenue' => Order::totalRevenue(),
        ];
        
        Router::view('admin/index', [
            'stats' => $stats,
            'page_title' => 'Dashboard',
            'current_page' => 'dashboard'
        ]);
    }
    
    public function users(): void
    {
        $this->checkAdmin();
        
        $users = User::all();
        
        Router::view('admin/users', [
            'users' => $users,
            'page_title' => 'Users',
            'current_page' => 'users'
        ]);
    }
    
    public function shops(): void
    {
        $this->checkAdmin();
        
        $sellers = User::findByRole('seller');
        $shops = [];
        
        foreach ($sellers as $seller) {
            $seller->product_count = Product::countBySeller($seller->id);
            $shops[] = $seller;
        }
        
        Router::view('admin/shops', [
            'shops' => $shops,
            'page_title' => 'Shops',
            'current_page' => 'shops'
        ]);
    }
    
    public function products(): void
    {
        $this->checkAdmin();
        
        $products = Product::all();
        
        $productsWithSeller = [];
        foreach ($products as $product) {
            $seller = User::find($product->seller_id);
            $product->seller_name = $seller ? $seller->name : 'Unknown';
            $productsWithSeller[] = $product;
        }
        
        Router::view('admin/products', [
            'products' => $productsWithSeller,
            'page_title' => 'Products',
            'current_page' => 'products'
        ]);
    }
    
    public function orders(): void
    {
        $this->checkAdmin();
        
        $orders = Order::all();
        
        Router::view('admin/orders', [
            'orders' => $orders,
            'page_title' => 'Orders',
            'current_page' => 'orders'
        ]);
    }
    
    public function deleteUser(int $id): void
    {
        $this->checkAdmin();
        
        $user = User::find($id);
        if ($user && $user->role !== 'admin') {
            $user->delete();
        }
        
        Router::redirect('/AFROTOUCH/public/admin/users');
    }
    
    public function deleteProduct(int $id): void
    {
        $this->checkAdmin();
        
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }
        
        Router::redirect('/AFROTOUCH/public/admin/products');
    }
}
