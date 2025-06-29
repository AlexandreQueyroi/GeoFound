<?php
namespace App\Models;
use App\Helpers\Database;

class Auth {
    public static function login($pseudoOrEmail, $password) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, pseudo, email, password, user_rank, desactivated, email_verified FROM users WHERE email = ? OR pseudo = ?');
        $stmt->execute([$pseudoOrEmail, $pseudoOrEmail]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
} 