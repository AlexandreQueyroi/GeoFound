<?php
session_start();
include_once __DIR__ . '/../../Helpers/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$input = json_decode(file_get_contents('php:
$avatar = $input['avatar'] ?? null;
if (!$avatar) {
    echo json_encode(['success' => false, 'message' => 'Aucun avatar reçu']);
    exit;
}

$user_id = $_SESSION['user_id'];
$db = \App\Helpers\Database::getConnection();
$stmt = $db->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->execute([$avatar, $user_id]);

echo json_encode(['success' => true]); 