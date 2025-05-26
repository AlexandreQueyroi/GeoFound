<?php
include_once(__DIR__ . '/api/bdd.php');

$messages = $conn->query('
    SELECT m.id, m.content, m.posted_at, m.mail_sent, u_from.pseudo AS sender_pseudo, u_to.pseudo AS receiver_pseudo, u_to.email AS receiver_email, um.sender_id, um.receiver_id
    FROM message m
    JOIN user_message um ON m.id = um.message_id
    JOIN users u_from ON um.sender_id = u_from.id
    JOIN users u_to ON um.receiver_id = u_to.id
    WHERE m.state = "sent" AND m.mail_sent = 0 AND m.posted_at < (NOW() - INTERVAL 5 SECOND)
')->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg) {
    $data = [
        'type' => 'notifyUnreadMessage',
        'sender_pseudo' => $msg['sender_pseudo'],
        'receiver_pseudo' => $msg['receiver_pseudo'],
        'receiver_email' => $msg['receiver_email'],
        'content' => $msg['content'],
        'link' => 'https://geofound.fr/me/inbox.php?friend_id=' . $msg['sender_id']
    ];
    $ch = curl_init('http://localhost/api/PHPMailer.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    $conn->prepare('UPDATE message SET mail_sent = 1 WHERE id = ?')->execute([$msg['id']]);
    echo 'Mail envoyé à ' . $msg['receiver_email'] . '<br>';
}
?> 