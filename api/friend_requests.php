<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['id'])) exit;
$uid = $_SESSION['id'];
$sql = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user1_id=u.id WHERE f.user2_id=:uid AND f.state='pending'";
$stmt = $conn->prepare($sql);
$stmt->execute(['uid'=>$uid]);
$received = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql2 = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user2_id=u.id WHERE f.user1_id=:uid AND f.state='pending'";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute(['uid'=>$uid]);
$sent = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['received'=>$received, 'sent'=>$sent]); 