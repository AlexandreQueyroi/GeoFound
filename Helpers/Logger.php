<?php
namespace App\Helpers;

class Logger {
    private static $logDir = __DIR__ . '/../storage/';
    
    public static function init() {
        
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        
        
        $logFile = self::$logDir . 'log_' . date('d-m-Y') . '.txt';
        if (!file_exists($logFile)) {
            $header = "======================\n";
            $header .= "Fichier de logs du " . date('d/m/Y') . "\n";
            $header .= "Format : [Date Heure] - [Type] - Message ---> Source\n";
            $header .= "======================\n\n";
            file_put_contents($logFile, $header);
            chmod($logFile, 0666);
        }
    }
    
    public static function log($type, $message, $source = '') {
        self::init();
        
        $logFile = self::$logDir . 'log_' . date('d-m-Y') . '.txt';
        $timestamp = date('d/m/Y H:i:s');
        $logEntry = "[{$timestamp}] - [{$type}] - {$message}";
        
        if (!empty($source)) {
            $logEntry .= " ---> {$source}";
        }
        
        $logEntry .= "\n";
        
        error_log($logEntry, 3, $logFile);
    }
    
    public static function error($message, $source = '') {
        self::log('ERROR', $message, $source);
    }
    
    public static function info($message, $source = '') {
        self::log('INFO', $message, $source);
    }
    
    public static function debug($message, $source = '') {
        self::log('DEBUG', $message, $source);
    }
    
    public static function warning($message, $source = '') {
        self::log('WARNING', $message, $source);
    }
} 