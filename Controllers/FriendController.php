<?php
namespace App\Controllers;

class FriendController {
    public function index($userId) {
        require __DIR__ . '/../Views/friend/index.php';
    }

    public function requests() {
        session_start();
        if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Non connecté']); exit; }
        $userId = $_SESSION['user_id'];
        $data = \App\Models\Friend::getRequests($userId);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function add() {
        session_start();
        if (!isset($_SESSION['user_id'])) { http_response_code(401); echo 'Non connecté'; exit; }
        $userId = $_SESSION['user_id'];
        $pseudo = trim($_POST['pseudo'] ?? '');
        $msg = \App\Models\Friend::addFriend($userId, $pseudo);
        echo $msg;
    }

    public function accept() {
        session_start();
        if (!isset($_SESSION['user_id'])) { http_response_code(401); echo 'Non connecté'; exit; }
        $userId = $_SESSION['user_id'];
        $id = intval($_POST['id'] ?? 0);
        $msg = \App\Models\Friend::acceptFriend($userId, $id);
        echo $msg;
    }

    public function refuse() {
        session_start();
        if (!isset($_SESSION['user_id'])) { http_response_code(401); echo 'Non connecté'; exit; }
        $userId = $_SESSION['user_id'];
        $id = intval($_POST['id'] ?? 0);
        $msg = \App\Models\Friend::refuseFriend($userId, $id);
        echo $msg;
    }
} 