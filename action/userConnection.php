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
if (!isset($_SESSION['user'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pseudo = htmlspecialchars($_POST["pseudo"]);
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT id, password, rank, connected FROM users WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password']) && $user['connected'] == false) {
            $_SESSION['user'] = $pseudo;
            // $_SESSION['rank'] = $user['rank'];
            $_SESSION['connected'] = true;
            $stmt = $conn->prepare("UPDATE users SET connected = true WHERE id = :id");
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['id'] = $user['id'];
        }

        header("Location: ../index.php");
        exit;
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["disconnect"])) {
        $stmt = $conn->prepare("UPDATE users SET connected = false WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->execute();
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
}

header("Location: ../index.php");
exit;
?>