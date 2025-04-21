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
    if (!isset($_SESSION['user']) && isset($_COOKIE['token'])) {
        $token = $_COOKIE['token'];
        $stmt = $conn->prepare("SELECT id, pseudo FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
    
        if ($user) {
            $_SESSION['user'] = $user['pseudo'];
            $_SESSION['rank'] = $user['rank'];
            $_SESSION['id'] = $user['id'];
    
            $stmt = $conn->prepare("UPDATE users SET connected = :date WHERE id = :id");
            $stmt->bindParam(':date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();

            if (isset($_SESSION['last_url'])) {
                header("Location: " . $_SESSION['last_url']);
                exit;
            } else {
                header("Location: /");
                exit;
            }
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pseudo = htmlspecialchars($_POST["pseudo"]);
        $password = $_POST["password"];
        $stmt = $conn->prepare("SELECT id, password, rank, connected FROM users WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password']) && $user['token'] == NULL) {
            $stmt = $conn->prepare("UPDATE users SET connected = :date WHERE id = :id");
            $stmt->bindParam(':date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['user'] = $pseudo;
            $_SESSION['id'] = $user['id'];
            $_SESSION['rank'] = $user['rank'];
            if (isset($_COOKIE['cookie_accepted'])) {
                $token = random_int(1000000000, 9999999999) . "-" . $user['id'] . "-" . time();
                setcookie("token", $token, time() + (7 * 86400), "/", "", false, true);
                
                $stmt = $conn->prepare("UPDATE users SET token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        if (isset($_SESSION['last_url'])) {
            header("Location: " . $_SESSION['last_url']);
        } else {
            header("Location: /");
        }
        exit;
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["disconnect"])) {
        $stmt = $conn->prepare("UPDATE users SET connected = NULL WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->execute();
        session_unset();
        session_destroy();
        header("Location: /");
        exit;
    }
}

header("Location: /error.php?error=403&message=Accès refusé, vous devez être connecté");
exit;
?>