<?php

namespace App\Controllers;

use App\Services\Router;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class DashboardController
{
    private function checkSeller(): void
    {
        if (!\App\Services\Auth::check()) {
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $user = \App\Services\Auth::user();
        if (!$user || $user->role !== 'seller') {
            Router::redirect('/AFROTOUCH/public/');
            return;
        }
    }
    
    public function overview(): void
    {
        $this->checkSeller();
        
        $current_page = 'overview';
        $page_title = 'Overview';
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $totalProducts = Product::countBySeller($seller_id);
        $totalOrders = Order::countBySeller($seller_id);
        $totalRevenue = Order::totalRevenueBySeller($seller_id);
        $recentOrders = Order::recentBySeller($seller_id, 5);
        $products = Product::findBySeller($seller_id);
        
        Router::view('dashboard/overview', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
            'products' => $products
        ]);
    }
    
    public function products(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'products';
        $page_title = 'Products';
        
        $products = Product::findBySeller($seller_id);
        
        Router::view('dashboard/products', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name,
            'products' => $products
        ]);
    }
    
    public function orders(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'orders';
        $page_title = 'Orders';
        
        $orders = Order::recentBySeller($seller_id, 20);
        
        Router::view('dashboard/orders', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name,
            'orders' => $orders
        ]);
    }
    
    public function analytics(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'analytics';
        $page_title = 'Analytics';
        
        $stats = Order::getStatsBySeller($seller_id);
        $ordersList = Order::recentBySeller($seller_id, 50);
        
        $pendingCount = $processingCount = $shippedCount = $deliveredCount = 0;
        foreach ($ordersList as $o) {
            if ($o->status === 'pending') $pendingCount++;
            elseif ($o->status === 'processing') $processingCount++;
            elseif ($o->status === 'shipped') $shippedCount++;
            elseif ($o->status === 'delivered') $deliveredCount++;
        }
        
        Router::view('dashboard/analytics', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name,
            'stats' => $stats,
            'ordersList' => $ordersList,
            'pendingCount' => $pendingCount,
            'processingCount' => $processingCount,
            'shippedCount' => $shippedCount,
            'deliveredCount' => $deliveredCount
        ]);
    }
    
    public function addProduct(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'add-product';
        $page_title = 'Add Product';
        
        Router::view('dashboard/add-product', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name
        ]);
    }
    
    public function storeProduct(): void
    {
        $seller_id = \App\Services\Auth::id();
        if (!$seller_id) {
            Router::redirect('/AFROTOUCH/public/login');
            return;
        }
        
        $imagePath = null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '/AFROTOUCH/public/uploads/products/' . $fileName;
                }
            }
        }
        
        $image = $imagePath ?? $_POST['image_url'] ?? null;
        
        $data = [
            'seller_id' => $seller_id,
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'category' => $_POST['category'] ?? 'Other',
            'stock' => $_POST['stock'] ?? 0,
            'status' => 'active',
            'image' => $image,
        ];
        
        Product::create($data);
        
        Router::redirect('/AFROTOUCH/public/dashboard/products');
    }
    
    public function settings(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'settings';
        $page_title = 'Settings';
        
        Router::view('dashboard/settings', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name
        ]);
    }
    
    public function shop(): void
    {
        $this->checkSeller();
        
        $seller_id = \App\Services\Auth::id();
        $seller = \App\Services\Auth::user();
        $seller_name = $seller ? $seller->name : 'Seller';
        
        $current_page = 'shop';
        $page_title = 'My Shop';
        
        Router::view('dashboard/shop', [
            'current_page' => $current_page,
            'page_title' => $page_title,
            'seller_name' => $seller_name
        ]);
    }
}