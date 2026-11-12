<?php
// includes/error_handler.php
// Custom error handler for the application

define('DEBUG_MODE', false);

set_error_handler(function($severity, $message, $filepath, $line) {
    $error_log_file = sys_get_temp_dir() . '/allo_service_errors.log';
    
    $error_message = date('[Y-m-d H:i:s]') . " $message in $filepath on line $line\n";
    
    error_log($error_message, 3, $error_log_file);
    
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        return false;
    }
    
    return true;
});

set_exception_handler(function($exception) {
    $error_log_file = sys_get_temp_dir() . '/allo_service_errors.log';
    
    $error_message = date('[Y-m-d H:i:s]') . " Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    $error_message .= "Stack trace: " . $exception->getTraceAsString() . "\n";
    
    error_log($error_message, 3, $error_log_file);
    
    if (!headers_sent()) {
        http_response_code(500);
    }
    
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo '<h1>Critical Error</h1>';
        echo '<pre>' . htmlspecialchars($exception->getMessage()) . '</pre>';
        echo '<pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
    } else {
        include __DIR__ . '/../500.php';
    }
    
    exit();
});

register_shutdown_function(function() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $error_log_file = sys_get_temp_dir() . '/allo_service_errors.log';
        
        $error_message = date('[Y-m-d H:i:s]') . " Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n";
        
        error_log($error_message, 3, $error_log_file);
        
        if (!headers_sent()) {
            http_response_code(500);
            include __DIR__ . '/../500.php';
        }
    }
});
?>
