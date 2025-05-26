<?php 
    error_reporting(E_ALL);
    ini_set("display_errors", 0);

    function errorhandler($errno, $errstr, $errfile, $errline)
    {
        $filename = __DIR__ . "/../log/log_" . date("d-m-Y") . ".txt";
        $new_file = !file_exists($filename);
        if ($new_file){
            $header = "======================\n";
            $header .= "Fichier de logs du " . date("d/m/Y") . "\n";
            $header .= "Format : [Date Heure] - [Type] - Message ---> Source\n";
            $header .= "======================\n\n";
            error_log($header, 3, $filename);
        }
        $message = "[".date("d/m/Y H:i:s",time())."] - [ERROR] - [$errno] - $errstr - $errfile:$errline\n";
        error_log($message, 3, $filename);
    }

    set_error_handler("errorhandler");

    function custom_log($logtype, $logmessage, $logfile)
    {
        $filename = __DIR__ . "/../log/log_" . date("d-m-Y") . ".txt";
        $new_file = !file_exists($filename);
        if ($new_file){
            $header = "======================\n";
            $header .= "Fichier de logs du " . date("d/m/Y") . "\n";
            $header .= "Format : [Date Heure] - [Type] - Message ---> Source\n";
            $header .= "======================\n\n";
            error_log($header, 3, $filename);
        }
        $message = "[".date("d/m/Y H:i:s",time())."] - [". strtoupper($logtype)."] - $logmessage ---> $logfile\n";
        error_log($message, 3, $filename);
    }

    // custom_log("type", "message", "path");