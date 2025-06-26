<?php

namespace App\Controllers;

use App\Models\Friend;
use App\Models\Message;

class UserController {
    public function edit($id) {
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        
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
} 