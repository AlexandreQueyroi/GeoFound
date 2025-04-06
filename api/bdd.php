<?php
define("servername", "localhost");
define("username", "geofound");
define("password", "geofound-2025");
define("dbname", "geofound");

try {
    $conn = new PDO("mysql:host=" . servername . ";dbname=" . dbname, username, password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}