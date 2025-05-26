<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['id'])) { echo 'no session'; exit; }
include_once('bdd.php');

$uid = $_SESSION['id'];
$sql = 'SELECT u.id, u.pseudo,
        (SELECT COUNT(*) FROM message m
         JOIN user_message um ON m.id = um.message_id
         WHERE um.sender_id = u.id
         AND um.receiver_id = :uid
         AND m.state = "sent") as unread_count
        FROM users u
        JOIN follow f ON ((f.user1_id = :uid AND f.user2_id = u.id)
        OR (f.user2_id = :uid AND f.user1_id = u.id))
        WHERE f.state = "accepted"';
$stmt = $conn->prepare($sql);
$stmt->execute(['uid' => $uid]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($friends); 