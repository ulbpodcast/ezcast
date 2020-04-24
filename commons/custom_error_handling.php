<?php

// This file is shared between server and recorder and should be kept identical in both projects
/*
 * Including this file has the following effects:
 * - If debug mode is enabled
 *     - PHP errors are printed to browser console
 *     - Error causing php shutdown are echoed
 * - Error causing php shutdown are written to logger
 *
 */

set_error_handler("error_handler");
register_shutdown_function("shutdown_handler");

//tests
//trigger_error("test error", E_USER_WARNING);
//trigger_error("error", E_USER_ERROR);


function get_error_type_str($errno)
{
    switch ($errno) {
        case E_ERROR:             return "error";
        case E_CORE_ERROR:        return "core_error";
        case E_COMPILE_ERROR:     return "compile_error";
        case E_RECOVERABLE_ERROR: return "recoverable_error";
        case E_USER_ERROR:        return "user_error";
        case E_PARSE:             return "parse";
        case E_USER_WARNING:      return "user_warning";
        case E_USER_NOTICE:       return "user_notice";
        case E_NOTICE:            return "notice";
        case E_WARNING:           return "warning";
        case E_STRICT:            return "strict";
        default:                  return "$errno"; //unknown, print no instead
    }
}

function is_critical_error($error_type)
{
    switch ($error_type) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_PARSE:
            return true;
        default:
            return false;
    }
}

/*
 * Custom error handler function.
 * Will output errors to browser console if in debug mode, and log php errors with E_ERROR in logger
 */
function error_handler($error_type, $errstr, $errfile, $errline)
{
    global $debug_mode;
    global $logger;
    
    $err_no_str = get_error_type_str($error_type);
    $full_err_str = "PHP [$err_no_str]: $errstr (file $errfile, line $errline)";
            
    if (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] != "" && $debug_mode) { //first conditions is to avoid printing error in case of php execution in console
        echo("<script>console.error('$full_err_str');</script>");
    }
    
    if (is_critical_error($error_type)) {
        //? This does not seem to work. Maybe db object is shut down before this function is run? execution stops at "$this->statement['insert_log']->execute();" in logger
        $logger->log(EventType::PHP, LogLevel::CRITICAL, $full_err_str, array("ezcast_error_handler"));
    }
    
    return false; // returning false here executes php internal error handler after this function
}

function shutdown_handler()
{ //will be called when php script ends.
    global $debug_mode;
    global $service;
    
    if ($debug_mode && !$service) {
        $lasterror = error_get_last();
        if (is_critical_error($lasterror['type'])) {
            $type_str = get_error_type_str($lasterror['type']);
            echo "[SHUTDOWN]<br/> type: $type_str <br/>msg: " . $lasterror['message'] . "<br/>file: " . $lasterror['file'] . "<br/>line: " . $lasterror['line'];
        }
    }
}
