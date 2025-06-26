<?php
include_once('bdd.php');
session_start();
if (!isset($_SESSION['user_id'])) exit;
$uid = $_SESSION['user_id'];
$id = intval($_POST['id'] ?? 0);
$stmt = $conn->prepare('DELETE FROM follow WHERE id=:id AND user2_id=:uid');
$stmt->execute(['id'=>$id,'uid'=>$uid]);
echo 'Demande refusée'; 