<?php

header('Content-Type: application/json');


include_once __DIR__ . '/../api/bdd.php';

$query = $conn->prepare("SELECT title, response FROM captcha WHERE enabled = 1 ORDER BY RAND() LIMIT 1");
$query->execute();
$captcha = $query->fetch(PDO::FETCH_ASSOC);

if ($captcha) {
    session_start();
    $response = [
        'question' => $captcha['title'],
        'answer' => $captcha['response']
    ];
    
    echo json_encode($response);
} else {
    die("Aucun captcha n'a été trouvé.");
}