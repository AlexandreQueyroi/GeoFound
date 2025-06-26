<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\Friend;
use App\Helpers\Database;

class MessageController
{
    public function inbox()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        $userId = $_SESSION['user_id'];
        $friends = Friend::getFriends($userId);
        require __DIR__ . '/../Views/message/inbox.php';
    }

    public function view()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $friendId = $_GET['friend_id'] ?? null;
        if (!$friendId) {
            http_response_code(400);
            echo json_encode(['error' => 'Ami non spécifié']);
            exit;
        }
        $messages = Message::getConversation($userId, $friendId);
        header('Content-Type: application/json');
        echo json_encode(['messages' => $messages]);
    }

    public function send()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        if (!$friendId || $content === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            exit;
        }
        $result = Message::sendMessage($userId, $friendId, $content);
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
} 