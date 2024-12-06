<?php
function handleError($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " Error: [$errno] $errstr in $errfile on line $errline\n";
    error_log($error_message, 3, "logs/error.log");

    if (ini_get('display_errors')) {
        echo "An error occurred. Please try again or contact support if the problem persists.";
    }
    
    return true;
}

set_error_handler("handleError");

function handleException($exception) {
    $error_message = date('Y-m-d H:i:s') . " Exception: " . $exception->getMessage() . 
        " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error_message, 3, "logs/error.log");

    if (ini_get('display_errors')) {
        echo "An error occurred. Please try again or contact support if the problem persists.";
    }
}

set_exception_handler("handleException"); 