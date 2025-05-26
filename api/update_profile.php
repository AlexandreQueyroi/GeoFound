<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['id'];
$pseudo = $_POST['pseudo'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Vérification du pseudo
if (!empty($pseudo)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE pseudo = ? AND id != ?");
    $stmt->execute([$pseudo, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ce pseudo est déjà utilisé']);
        exit;
    }
}

// Vérification de l'email
if (!empty($email)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit;
    }
}

// Mise à jour du profil
$updates = [];
$params = [];

if (!empty($pseudo)) {
    $updates[] = "pseudo = ?";
    $params[] = $pseudo;
}

if (!empty($email)) {
    $updates[] = "email = ?";
    $params[] = $email;
    // Générer un token de vérification
    $verification_token = bin2hex(random_bytes(32));
    $updates[] = "email_verification_token = ?";
    $params[] = $verification_token;
    // Envoyer l'email de vérification
    // TODO: Implémenter l'envoi d'email
}

if (!empty($password)) {
    $updates[] = "password = ?";
    $params[] = password_hash($password, PASSWORD_DEFAULT);
}

if (!empty($updates)) {
    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
}

echo json_encode([
    'success' => true,
    'requiresEmailVerification' => !empty($email)
]);
