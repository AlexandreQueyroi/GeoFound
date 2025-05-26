<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['id'])) exit;
$uid = $_SESSION['id'];
$id = intval($_POST['id'] ?? 0);
$stmt = $conn->prepare('UPDATE follow SET state="accepted" WHERE id=:id AND user2_id=:uid');
$stmt->execute(['id'=>$id,'uid'=>$uid]);
echo 'Ami accepté'; 