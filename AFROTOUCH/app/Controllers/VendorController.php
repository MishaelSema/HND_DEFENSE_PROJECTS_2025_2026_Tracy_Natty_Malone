<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\Router;

class VendorController
{
    public function index(): void
    {
        $sellers = User::findByRole('seller');
        
        $shops = [];
        foreach ($sellers as $seller) {
            $shops[] = [
                'user' => $seller,
                'product_count' => Product::countBySeller($seller->id)
            ];
        }
        
        Router::view('vendors/shops', ['shops' => $shops]);
    }
    
    public function shop(int $id): void
    {
        $vendor = User::find($id);
        
        if (!$vendor || $vendor->role !== 'seller') {
            Router::view('errors/404');
            return;
        }
        
        $products = Product::findActive($id);
        
        Router::view('vendors/shop', [
            'vendor' => $vendor,
            'products' => $products
        ]);
    }
    
    public function all(): void
    {
        $sellers = User::findByRole('seller');
        
        $shops = [];
        foreach ($sellers as $seller) {
            $shops[] = [
                'user' => $seller,
                'product_count' => Product::countBySeller($seller->id)
            ];
        }
        
        Router::view('vendors/shops', ['shops' => $shops]);
    }
}
