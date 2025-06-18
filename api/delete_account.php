<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['id'];

$data = [
    'user' => [],
    'posts' => [],
    'followers' => [],
    'following' => []
];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT p.*, pc.content FROM post p JOIN post_content pc ON p.content_id = pc.id WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$data['posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT u.* FROM follow f JOIN users u ON f.user1_id = u.id WHERE f.user2_id = ? AND f.state = 'accepted'");
$stmt->execute([$user_id]);
$data['followers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT u.* FROM follow f JOIN users u ON f.user2_id = u.id WHERE f.user1_id = ? AND f.state = 'accepted'");
$stmt->execute([$user_id]);
$data['following'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$deleted_accounts_dir = __DIR__ . '/../deleted_accounts';
if (!file_exists($deleted_accounts_dir)) {
    mkdir($deleted_accounts_dir, 0777, true);
}

$filename = $deleted_accounts_dir . '/account_' . $user_id . '_' . date('Y-m-d_H-i-s') . '.json';
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

session_destroy();

echo json_encode(['success' => true]);
