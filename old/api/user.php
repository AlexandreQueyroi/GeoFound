<?php
include_once(__DIR__ . '/../api/bdd.php');
session_start();
if (isset($_GET["checkuser"])) {
    $user = htmlspecialchars($_GET["checkuser"]);
    $stmt = $conn->prepare("SELECT id FROM users WHERE pseudo = :pseudo");
    $stmt->bindParam(':pseudo', $user, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "exist";
    }
    exit;
}
if (isset($_GET["checkemail"])) {
    $user = htmlspecialchars($_GET["checkemail"]);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $user, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "exist";
    }
    exit;
}