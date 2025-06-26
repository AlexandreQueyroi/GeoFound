<?php
namespace App\Models;
use App\Helpers\Database;

class Auth {
    public static function login($pseudoOrEmail, $password) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR pseudo = ?');
        $stmt->execute([$pseudoOrEmail, $pseudoOrEmail]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['user'] = $user['pseudo'];
            return true;
        }
        return false;
    }
    public static function register($data) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO users (email, password, username) VALUES (?, ?, ?)');
        $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['username']
        ]);
        return $pdo->lastInsertId();
    }
} 