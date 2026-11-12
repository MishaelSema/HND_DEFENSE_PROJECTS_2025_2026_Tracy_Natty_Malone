<?php

namespace App\Controllers;

use App\Services\Router;

class StaticController
{
    public function contact(): void
    {
        Router::view('static/contact', ['page_title' => 'Contact Us']);
    }
    
    public function faq(): void
    {
        Router::view('static/faq', ['page_title' => 'FAQ']);
    }
    
    public function terms(): void
    {
        Router::view('static/terms', ['page_title' => 'Terms of Service']);
    }
    
    public function privacy(): void
    {
        Router::view('static/privacy', ['page_title' => 'Privacy Policy']);
    }
    
    public function sellerGuide(): void
    {
        Router::view('static/seller-guide', ['page_title' => 'Seller Guide']);
    }
    
    public function pricing(): void
    {
        Router::view('static/pricing', ['page_title' => 'Pricing Plans']);
    }
    
    public function verification(): void
    {
        Router::view('static/verification', ['page_title' => 'Seller Verification']);
    }
    
    public function help(): void
    {
        Router::view('static/help', ['page_title' => 'Help Center']);
    }
    
    public function shipping(): void
    {
        Router::view('static/shipping', ['page_title' => 'Shipping Information']);
    }
    
    public function returns(): void
    {
        Router::view('static/returns', ['page_title' => 'Returns & Refunds']);
    }
}
