<?php
session_start();
include_once('bdd.php');

$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo 'Lien invalide.';
    exit;
}

$stmt = $conn->prepare("SELECT id, email FROM users WHERE email_verification_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo 'Lien de vérification invalide ou expiré.';
    exit;
}

$stmt = $conn->prepare("UPDATE users SET email_verification_token = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

echo 'Votre nouvelle adresse email a bien été vérifiée !';
