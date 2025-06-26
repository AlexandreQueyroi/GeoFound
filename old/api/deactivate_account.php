<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE users SET desactivated = 1 WHERE id = ?");
$stmt->execute([$user_id]);

echo json_encode(['success' => true]);
