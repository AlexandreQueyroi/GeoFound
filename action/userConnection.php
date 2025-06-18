<?php
include_once(__DIR__ . '/../api/bdd.php');
include_once(__DIR__ . '/../api/log.php');
session_start();

if (!isset($_SESSION['user'])) {
    if (isset($_COOKIE['token'])) {
        $token = $_COOKIE['token'];
        $stmt = $conn->prepare("SELECT id, pseudo, rank FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = $user['pseudo'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['rank'] = $user['rank'];

            custom_log("INFO", ' User ' . $user['pseudo'] . ' with ID : ' . $user['id'] . ' just auto relogged in.', "userConnection.php");

            $stmt = $conn->prepare("UPDATE users SET connected = :date WHERE id = :id");
            $currentDate = date("Y-m-d H:i:s");
            $stmt->bindParam(':date', $currentDate, PDO::PARAM_STR);
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();

            if (isset($_SESSION['last_url'])) {
                header("Location: " . $_SESSION['last_url']);
                exit;
            } else {
                header("Location: /");
                exit;
            }
        } else {
            setcookie("token", "", time() - 3600, "/", "", false, true);
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pseudo = htmlspecialchars($_POST["pseudo"]);
        $password = $_POST["password"];
        $stmt = $conn->prepare("SELECT id, password, rank FROM users WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $pseudo;
            $_SESSION['id'] = $user['id'];
            $_SESSION['rank'] = $user['rank'];

            custom_log("INFO", ' User ' . $pseudo . ' with ID : ' . $user['id'] . ' just logged in.', "userConnection.php");

            $stmt = $conn->prepare("UPDATE users SET connected = :date WHERE id = :id");
            $currentDate = date("Y-m-d H:i:s");
            $stmt->bindParam(':date', $currentDate, PDO::PARAM_STR);
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();

            if (isset($_COOKIE['cookie'])) {
                $token = random_int(1000000000, 9999999999) . "-" . $user['id'] . "-" . time();
                $token = password_hash($token, PASSWORD_DEFAULT);
                setcookie("token", $token, time() + (7 * 86400), "/", "", false, true);

                $stmt = $conn->prepare("UPDATE users SET token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            if (isset($_SESSION['last_url'])) {
                header("Location: " . $_SESSION['last_url']);
            } else {
                header("Location: /");
            }
            exit;
        }
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["disconnect"])) {
        $stmt = $conn->prepare("UPDATE users SET connected = NULL, token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        custom_log("INFO", ' User ' . $_SESSION['user'] . ' with ID : ' . $_SESSION['id'] . ' just logged out.', "userConnection.php");
        
        session_unset();
        session_destroy();
        setcookie("token", "", time() - 3600, "/", "", false, true);
        
        header("Location: /");
        exit;
    }
}

custom_log("ERROR", ' User ' . ($_SESSION['user'] ?? 'NULL') . ' with ID : ' . ($_SESSION['id'] ?? 'NULL') . " and server variable : " . print_r($_SERVER ?? 'SERVER VARIABLE NOT FOUND', true) . ' just failed connection check.', "userConnection.php");
header("Location: /");
exit;
?>