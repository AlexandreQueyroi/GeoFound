<?php
namespace App\Controllers;

class ErrorController {
    public function error403() {
        require __DIR__ . '/../Views/error/403.php';
    }
    public function error404() {
        require __DIR__ . '/../Views/error/404.php';
    }
    public function error500() {
        require __DIR__ . '/../Views/error/500.php';
    }
} 