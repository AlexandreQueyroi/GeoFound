<?php
include_once('bdd.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = $_SESSION['user_id'];
$state = $_POST['state'] ?? 'like';

if ($post_id === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de post invalide']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, state FROM reaction WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing_reaction = $stmt->fetch();

    if ($existing_reaction) {
        if ($existing_reaction['state'] === $state) {
            $stmt = $conn->prepare("DELETE FROM reaction WHERE id = ?");
            $stmt->execute([$existing_reaction['id']]);
            echo json_encode(['action' => 'removed']);
        } else {
            $stmt = $conn->prepare("UPDATE reaction SET state = ?, react_at = NOW() WHERE id = ?");
            $stmt->execute([$state, $existing_reaction['id']]);
            echo json_encode(['action' => 'updated']);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO reaction (post_id, user_id, state) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $state]);
        echo json_encode(['action' => 'added']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?> 