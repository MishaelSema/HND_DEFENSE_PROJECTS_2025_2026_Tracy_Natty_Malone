<?php

require_once __DIR__ . '/config/Database.php';

require_once __DIR__ . '/app/Services/Session.php';
\App\Services\Session::start();

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

require_once __DIR__ . '/app/Services/Router.php';
require_once __DIR__ . '/app/Services/Session.php';
require_once __DIR__ . '/app/Services/Auth.php';
