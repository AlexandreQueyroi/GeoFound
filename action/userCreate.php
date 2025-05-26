<?php
include_once(__DIR__ . '/../api/bdd.php');
session_start();

if (isset($_POST['newuser']) && isset($_POST['newpass']) && isset($_POST['newpass_confirm'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = password_hash(htmlspecialchars($_POST['newpass']), CRYPT_SHA256);
        $pseudo = htmlspecialchars($_POST['newuser']);
        $email = htmlspecialchars($_POST['newmail']);
        $sql = "INSERT INTO users (pseudo, password, email) VALUES (:pseudo, :password, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conn->lastInsertId();

        $state = "success";
        $data = array(
            'username' => $pseudo,
            'id' => $id,
            'type' => 'confirmCreate',
            'email' => htmlspecialchars($_POST['newmail'])
        );
        $options = array(
            'http' => array(
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents('https://geofound.fr/api/PHPMailer.php', false, $context);
        var_dump($result);
        var_dump($http_response_header);
        if ($result === false) {
            $err = error_get_last();
            print_r($err);
            exit;
        }

        header("Location: /accountCreated?status=" . $state);
        exit;

    }
}
header("Location: /");
?>