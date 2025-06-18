<?php
session_start();
include_once(__DIR__ . '/api/bdd.php');

$user_id = $_SESSION['user']['id'];
$reward_id = $_POST['reward_id'];

$stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_points = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT cost_points FROM reward WHERE id = ?");
$stmt->execute([$reward_id]);
$cost = $stmt->fetchColumn();

if ($user_points >= $cost) {
    $new_points = $user_points - $cost;
    $stmt = $conn->prepare("UPDATE users SET points = ? WHERE id = ?");
    $stmt->execute([$new_points, $user_id]);

    echo "<script>alert('Récompense débloquée !');</script>";
} else {
    echo "<script>alert('Pas assez de points.');</script>";
}

header('Location: /reward.php');
exit();