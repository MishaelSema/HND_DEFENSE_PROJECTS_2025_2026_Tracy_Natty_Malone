<?php

namespace App\Services;

use App\Models\User;

class Auth
{
    public static function login($user): void
    {
        Session::set('user_id', $user->id);
    }

    public static function logout(): void
    {
        Session::forget('user_id');
    }

    public static function user(): ?User
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }
        
        $user = User::find($id);
        
        if (!$user) {
            self::logout();
            return null;
        }
        
        return $user;
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }
}
