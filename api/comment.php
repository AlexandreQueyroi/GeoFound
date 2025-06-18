<?php
include_once('bdd.php');
session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = $_SESSION['id'];
$content = trim($_POST['content'] ?? '');

if ($post_id === 0 || empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO comment (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);
    
    $comment_id = $conn->lastInsertId();
    $stmt = $conn->prepare("
        SELECT c.*, u.pseudo 
        FROM comment c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'comment' => [
            'id' => $comment['id'],
            'content' => $comment['content'],
            'user' => $comment['pseudo'],
            'date' => $comment['comment_at']
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?> 