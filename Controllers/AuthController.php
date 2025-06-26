<?php
namespace App\Controllers;

use App\Models\Auth;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudoOrEmail = $_POST['pseudo'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (Auth::login($pseudoOrEmail, $password)) {
                header('Location: ' . ($_SESSION['last_url'] ?? '/'));
                unset($_SESSION['last_url']);
                exit;
            } else {
                $_SESSION['login_error'] = "Identifiants invalides.";
                $_SESSION['show_login_modal'] = true;
                header('Location: ' . ($_SESSION['last_url'] ?? '/'));
                exit;
            }
        }
        header('Location: /');
        exit;
    }
    
    public function register() {
        require __DIR__ . '/../Views/auth/register.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
} 