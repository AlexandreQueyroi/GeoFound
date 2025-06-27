<?php

namespace App\Controllers;

use App\Models\Friend;
use App\Models\Message;

class UserController {
    public function edit($id = null) {
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        if ($id === null) {
            $id = $_SESSION['user_id'];
        }
        $user = \App\Models\User::find($id);
        require_once __DIR__ . '/../Views/user/edit.php';
    }

    public function inbox() {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        $friendModel = new Friend();
        $messageModel = new Message();
        
        $friends = $friendModel->getFriendsWithUnread($_SESSION['user_id']);
        $selected_friend = isset($_GET['friend']) ? intval($_GET['friend']) : null;
        $messages = [];
        
        if ($selected_friend) {
            $messages = $messageModel->getConversation($_SESSION['user_id'], $selected_friend);
        }
        
        require_once __DIR__ . '/../Views/user/inbox.php';
    }

    public function profile($id = null) {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        if ($id === null) {
            $id = $_SESSION['user_id'];
        }

        
        $user = \App\Models\User::find($id);
        
        $posts = \App\Models\Post::findByUser($id);
        $post_count = is_array($posts) ? count($posts) : 0;
        
        $pdo = \App\Helpers\Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM follow WHERE user2_id = ? AND state = "accepted"');
        $stmt->execute([$id]);
        $followers_count = $stmt->fetchColumn() ?: 0;
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM follow WHERE user1_id = ? AND state = "accepted"');
        $stmt->execute([$id]);
        $following_count = $stmt->fetchColumn() ?: 0;

        require __DIR__ . '/../Views/user/profile.php';
    }

    public function exportJson() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        $user = \App\Models\User::find($_SESSION['user_id']);
        $friends = \App\Models\Friend::getFriends($_SESSION['user_id']);
        $data = [
            'pseudo' => $user['pseudo'] ?? '',
            'email' => $user['email'] ?? '',
            'date_inscription' => $user['created_at'] ?? '',
            'amis' => array_map(function($f) { return $f['pseudo']; }, $friends)
        ];
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="profil.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function exportPdf() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo 'Non autorisé';
            exit;
        }
        $user = \App\Models\User::find($_SESSION['user_id']);
        $friends = \App\Models\Friend::getFriends($_SESSION['user_id']);
        require_once __DIR__ . '/../vendor/setasign/fpdf/fpdf.php';
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'Profil utilisateur',0,1,'C');
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,10,'Pseudo : ' . ($user['pseudo'] ?? ''),0,1);
        $pdf->Cell(0,10,'Email : ' . ($user['email'] ?? ''),0,1);
        if (!empty($user['created_at'])) {
            $pdf->Cell(0,10,'Date inscription : ' . $user['created_at'],0,1);
        }
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'Amis :',0,1);
        $pdf->SetFont('Arial','',12);
        foreach ($friends as $f) {
            $pdf->Cell(0,8,'- '.$f['pseudo'],0,1);
        }
        $pdf->Output('D', 'profil.pdf');
        exit;
    }
} 