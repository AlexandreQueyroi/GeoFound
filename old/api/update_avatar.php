<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php:
$user_id = $_SESSION['user_id'];

$avatar_data = "base64_encoded_avatar_data";

$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->execute([$avatar_data, $user_id]);

echo json_encode([
    'success' => true,
    'avatarUrl' => 'data:image/jpeg;base64,' . $avatar_data
]);
