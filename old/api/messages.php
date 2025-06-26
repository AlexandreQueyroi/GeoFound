<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['user_id'])) exit;
$uid = $_SESSION['user_id'];
$fid = intval($_GET['friend_id'] ?? 0);
if ($fid == 0) exit('[]');
$stmt = $conn->prepare('SELECT * FROM follow WHERE ((user1_id=:uid AND user2_id=:fid) OR (user2_id=:uid AND user1_id=:fid)) AND state="accepted"');
$stmt->execute(['uid'=>$uid,'fid'=>$fid]);
if ($stmt->rowCount() == 0) exit('[]');
$sql = 'SELECT m.id, m.content, m.posted_at, um.sender_id, m.state 
        FROM message m 
        JOIN user_message um ON m.id = um.message_id 
        WHERE (um.sender_id = :uid AND um.receiver_id = :fid) 
           OR (um.sender_id = :fid AND um.receiver_id = :uid) 
        ORDER BY m.posted_at ASC';
$stmt = $conn->prepare($sql);
$stmt->execute(['uid'=>$uid, 'fid'=>$fid]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$result = [];
foreach ($messages as $m) {
    $result[] = [
        'content' => $m['content'],
        'sent' => ($m['sender_id'] == $uid),
        'time' => $m['posted_at'],
        'state' => $m['state']
    ];
}
$update = $conn->prepare('UPDATE message m
    JOIN user_message um ON m.id = um.message_id
    SET m.state = "read"
    WHERE um.receiver_id = :uid AND um.sender_id = :fid AND m.state = "sent"');
$update->execute(['uid' => $uid, 'fid' => $fid]);
header('Content-Type: application/json');
echo json_encode($result); 