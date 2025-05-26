<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['id'])) exit;
$uid = $_SESSION['id'];

$sql = 'SELECT m.id, m.content, m.posted_at, u.pseudo, um.sender_id
        FROM message m
        JOIN user_message um ON m.id = um.message_id
        JOIN users u ON um.sender_id = u.id
        WHERE um.receiver_id = :uid AND um.sender_id != :uid
        ORDER BY m.posted_at DESC
        LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->execute(['uid' => $uid]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($msg ? $msg : []); 