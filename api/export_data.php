<?php
session_start();
include_once('bdd.php');

if (!isset($_SESSION['id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$user_id = $_SESSION['id'];
$format = $_GET['format'] ?? 'json';

$data = [
    'user' => [],
    'posts' => [],
    'followers' => [],
    'following' => []
];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT p.*, pc.content FROM post p JOIN post_content pc ON p.content_id = pc.id WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$data['posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT u.* FROM follow f JOIN users u ON f.user1_id = u.id WHERE f.user2_id = ? AND f.state = 'accepted'");
$stmt->execute([$user_id]);
$data['followers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT u.* FROM follow f JOIN users u ON f.user2_id = u.id WHERE f.user1_id = ? AND f.state = 'accepted'");
$stmt->execute([$user_id]);
$data['following'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'pdf') {
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    
    $pdf->Cell(0, 10, 'Données utilisateur', 0, 1);
    $pdf->Cell(0, 10, 'Pseudo: ' . $data['user']['pseudo'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $data['user']['email'], 0, 1);
    
    $pdf->Cell(0, 10, 'Posts', 0, 1);
    foreach ($data['posts'] as $post) {
        $pdf->Cell(0, 10, $post['description'], 0, 1);
    }
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="mes-donnees.pdf"');
    echo $pdf->Output('', 'S');
} else {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="mes-donnees.json"');
    echo json_encode($data, JSON_PRETTY_PRINT);
}
