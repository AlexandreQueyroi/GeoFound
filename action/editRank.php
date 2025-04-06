<?php
include_once '../api/bdd.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM ranks WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $rank = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newRank = htmlspecialchars($_POST["grade-name"]);
        $newColor = htmlspecialchars($_POST["grade-color"]);
        $newColor = str_replace("#", "", $newColor);

        $updateStmt = $conn->prepare("UPDATE ranks SET name = :name, color = :color WHERE id = :id");
        $updateStmt->bindParam(':name', $newRank, PDO::PARAM_STR);
        $updateStmt->bindParam(':color', $newColor, PDO::PARAM_STR);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        header("Location: ../admin/rank.php");
        exit;
    }
}