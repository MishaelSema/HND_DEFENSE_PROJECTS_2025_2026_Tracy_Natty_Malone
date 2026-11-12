<?php

session_start();

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

require_once __DIR__ . '/../bootstrap.php';

require_once __DIR__ . '/../routes/web.php';

\App\Services\Router::run();
