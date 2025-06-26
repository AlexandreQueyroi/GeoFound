<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];
$pseudo = $_POST['pseudo'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($pseudo)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE pseudo = ? AND id != ?");
    $stmt->execute([$pseudo, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ce pseudo est déjà utilisé']);
        exit;
    }
}

if (!empty($email)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit;
    }
}

$updates = [];
$params = [];

if (!empty($pseudo)) {
    $updates[] = "pseudo = ?";
    $params[] = $pseudo;
}

if (!empty($email)) {
    $updates[] = "email = ?";
    $params[] = $email;
    $verification_token = bin2hex(random_bytes(32));
    $updates[] = "email_verification_token = ?";
    $params[] = $verification_token;
    $user_stmt = $conn->prepare("SELECT pseudo FROM users WHERE id = ?");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user_data ? $user_data['pseudo'] : '';
    $mailData = [
        'type' => 'verifyEmail',
        'email' => $email,
        'username' => $username,
        'token' => $verification_token
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mailData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("Mail response: " . $response . " - HTTP Code: " . $httpCode);
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
