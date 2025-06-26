<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['user_id'])) exit;
$uid = $_SESSION['user_id'];
$to = intval($_POST['to'] ?? 0);
$content = trim($_POST['message'] ?? '');
if ($to == 0 || $content == '') exit;
$stmt = $conn->prepare('SELECT * FROM follow WHERE ((user1_id=:uid AND user2_id=:to) OR (user2_id=:uid AND user1_id=:to)) AND state="accepted"');
$stmt->execute(['uid'=>$uid,'to'=>$to]);
if ($stmt->rowCount() == 0) exit;
$stmt = $conn->prepare('INSERT INTO message (posted_at, content, state) VALUES (NOW(), :content, "sent")');
$stmt->execute(['content'=>$content]);
$message_id = $conn->lastInsertId();

$stmt = $conn->prepare('INSERT INTO user_message (message_id, sender_id, receiver_id) VALUES (:message_id, :sender_id, :receiver_id)');
$stmt->execute([
    'message_id' => $message_id,
    'sender_id' => $uid,
    'receiver_id' => $to
]);
echo 'ok'; 