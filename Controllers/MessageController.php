<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\Friend;
use App\Helpers\Database;

class MessageController {
    public function inbox() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['show_login_modal'] = true;
            header('Location: /');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $friends = Friend::getFriends($userId);
        
        
        error_log("MessageController::inbox - User ID: $userId, Friends count: " . count($friends));
        
        require __DIR__ . '/../Views/message/inbox.php';
    }
    public function view() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $friendId = $_GET['friend_id'] ?? null;
        
        
        error_log("MessageController::view - User ID: $userId, Friend ID: $friendId");
        
        if (!$friendId) {
            http_response_code(400);
            echo json_encode(['error' => 'Ami non spécifié']);
            exit;
        }
        $messages = Message::getConversation($userId, $friendId);
        
        
        error_log("MessageController::view - Messages count: " . count($messages));
        
        header('Content-Type: application/json');
        echo json_encode(['messages' => $messages]);
    }
    public function friends() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { 
            http_response_code(401); 
            echo json_encode(['error' => 'Non connecté']); 
            exit; 
        }
        try {
            $userId = $_SESSION['user_id'];
            $friends = \App\Models\Friend::getFriendsWithUnread($userId);
            echo json_encode($friends);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
    public function conversation() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { 
            http_response_code(401); 
            echo json_encode(['error' => 'Non connecté']); 
            exit; 
        }
        try {
            $userId = $_SESSION['user_id'];
            $friendId = intval($_GET['friend_id'] ?? 0);
            
            if (!$friendId) { 
                echo json_encode([]); 
                exit; 
            }

            
            if (!\App\Models\Friend::areFriends($userId, $friendId)) {
                http_response_code(403);
                echo json_encode(['error' => 'Vous n\'êtes pas ami avec cet utilisateur']);
                exit;
            }

            $messages = \App\Models\Message::getConversation($userId, $friendId);
            echo json_encode($messages);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
    public function send() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        
        
        error_log("MessageController::send - User ID: $userId, Friend ID: $friendId, Content: $content");
        
        if (!$friendId || $content === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            exit;
        }
        $result = Message::sendMessage($userId, $friendId, $content);
        
        
        error_log("MessageController::send - Result: " . ($result ? 'success' : 'failed'));
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
} 