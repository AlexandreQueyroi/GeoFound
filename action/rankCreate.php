<?php
include_once '../api/bdd.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $rank = htmlspecialchars($_POST["grade-name"]);
        $color = htmlspecialchars($_POST["grade-color"]);
        $color = str_replace("#", "", $color);
        $stmt = $conn->prepare("INSERT INTO ranks (name, color) VALUES (:rank, :color)");
        $stmt->bindParam(':rank', $rank, PDO::PARAM_STR);
        $stmt->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt->execute();
        header("Location: ../admin/rank.php");

    }
}
