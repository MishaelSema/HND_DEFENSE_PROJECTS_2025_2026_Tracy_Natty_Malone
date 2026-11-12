<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\Router;
use App\Services\Session;
use App\Services\Auth;

class ProductController
{
    public function index(): void
    {
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        
        $products = Product::findActive();
        
        if ($category) {
            $products = array_filter($products, fn($p) => $p->category === $category);
        }
        
        if ($search) {
            $searchLower = strtolower($search);
            $products = array_filter($products, fn($p) => 
                strpos(strtolower($p->name), $searchLower) !== false ||
                strpos(strtolower($p->description ?? ''), $searchLower) !== false
            );
        }
        
        $categories = ['Clothing', 'Jewelry', 'Home Decor', 'Art', 'Accessories', 'Crafts', 'Food'];
        
        Router::view('products/index', [
            'products' => $products,
            'categories' => $categories,
            'selected_category' => $category,
            'search' => $search
        ]);
    }
    
    public function show(int $id): void
    {
        $product = Product::find($id);
        
        if (!$product) {
            Router::view('errors/404');
            return;
        }
        
        $seller = User::find($product->seller_id);
        
        Router::view('products/show', [
            'product' => $product,
            'seller' => $seller
        ]);
    }
}
