<?php
session_start();
$_SESSION['last_url'] = $_SERVER['REQUEST_URI'];
if (!isset($_SESSION['user'])) {
    header('Location: /action/userConnection.php');
    exit();
}


?>