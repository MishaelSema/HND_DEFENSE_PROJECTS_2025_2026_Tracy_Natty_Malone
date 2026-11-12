<?php

namespace App\Services;

class Session
{
    private static bool $initialized = false;
    
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$initialized = true;
        }
    }
    
    public static function init(): void
    {
        self::start();
    }
    
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
    }

    public static function flash(string $key, $value = null): void
    {
        self::start();
        if ($value === null) {
            $value = self::get($key);
            self::forget($key);
        } else {
            self::set($key, $value);
        }
    }
}
