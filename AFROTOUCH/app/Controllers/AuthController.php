<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\Router;
use App\Services\Session;
use App\Services\Auth;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                Router::redirect('/AFROTOUCH/public/admin');
            } elseif ($user->role === 'seller') {
                Router::redirect('/AFROTOUCH/public/dashboard');
            } else {
                Router::redirect('/AFROTOUCH/public/');
            }
            return;
        }
        
        Router::view('auth/login');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                Router::redirect('/AFROTOUCH/public/admin');
            } elseif ($user->role === 'seller') {
                Router::redirect('/AFROTOUCH/public/dashboard');
            } else {
                Router::redirect('/AFROTOUCH/public/');
            }
            return;
        }
        
        Router::view('auth/register');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            Auth::login($user);
            
            if ($user->role === 'admin') {
                Router::redirect('/AFROTOUCH/public/admin');
            } elseif ($user->role === 'seller') {
                Router::redirect('/AFROTOUCH/public/dashboard');
            } else {
                Router::redirect('/AFROTOUCH/public/');
            }
        }

        Session::flash('error', 'Invalid email or password');
        Router::redirect('/AFROTOUCH/public/login');
    }

    public function register(): void
    {
        $role = $_POST['role'] ?? 'buyer';
        
        $data = [
            'email' => $_POST['email'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'role' => $role,
            'phone' => $_POST['phone'] ?? null,
            'country' => $_POST['country'] ?? null,
            'address' => $_POST['address'] ?? null,
        ];
        
        // Additional fields for sellers
        if ($role === 'seller') {
            $data['name'] = $_POST['name'] ?? '';
            $data['shop_name'] = $_POST['shop_name'] ?? '';
            $data['shop_description'] = $_POST['shop_description'] ?? null;
            $data['shop_location'] = $_POST['shop_location'] ?? null;
        } else {
            $data['name'] = $_POST['email'] ?? ''; // Use email as name for buyers initially
        }

        if (User::findByEmail($data['email'])) {
            Session::flash('error', 'Email already exists');
            Router::redirect('/AFROTOUCH/public/register');
        }

        $user = User::create($data);

        Auth::login($user);
        
        if ($user->role === 'admin') {
            Router::redirect('/AFROTOUCH/public/admin');
        } elseif ($user->role === 'seller') {
            Router::redirect('/AFROTOUCH/public/dashboard');
        } else {
            Router::redirect('/AFROTOUCH/public/');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Router::redirect('/AFROTOUCH/public/');
    }
}