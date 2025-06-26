<?php
require_once __DIR__ . '/../../vendor/autoload.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(null);
    exit;
}

try {
    $db = \App\Helpers\Database::getConnection();
    $uid = $_SESSION['user_id'];

    $sql = 'SELECT m.id, m.content, m.created_at as posted_at, u.username as pseudo, m.sender_id
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.receiver_id = :uid AND m.sender_id != :uid AND m.status = "sent"
            ORDER BY m.created_at DESC
            LIMIT 1';
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['uid' => $uid]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    if ($msg) {
        echo json_encode($msg);
    } else {
        echo json_encode(null);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(null);
}
?> 