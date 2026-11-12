<?php

namespace App\Controllers;

use App\Services\Router;
use App\Services\Auth;

class HomeController
{
    public function index(): void
    {
        if (Auth::check()) {
            Router::redirect('/AFROTOUCH/public/dashboard');
            return;
        }
        
        Router::view('home/index');
    }
}