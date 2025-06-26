<?php
namespace App\Helpers;

class Database {
    public static function getConnection() {
        $host = 'localhost';
        $dbname = 'geofound';
        $user = 'geofound';
        $pass = 'geofound-2025';
        try {
            $pdo = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
} 