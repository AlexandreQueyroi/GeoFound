<?php
session_start();
$_SESSION['last_url'] = $_SERVER['REQUEST_URI'];
if (!isset($_SESSION['user'])) {
    echo "<script>console.log('not logged');</script>";
    header('Location: /action/userConnection.php');
    exit();
} else {
    echo "<script>console.log('logged');</script>";
}
include_once(__DIR__ . '/build/header.php');
include_once(__DIR__ . '/../api/bdd.php');
// if (!($_SESSION['user_rank'] === "admin" || $_SESSION['user_rank'] === "mod")) {
//     header("Location: /index.php");
//     exit();
// }
?>