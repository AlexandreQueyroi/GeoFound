<?php
namespace App\Models;
use App\Helpers\Database;

class Message {
    public static function getInbox($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT m.*, um.sender_id, um.receiver_id 
            FROM message m 
            JOIN user_message um ON m.id = um.message_id 
            WHERE um.receiver_id = ?
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function getConversation($userId, $friendId) {
        $pdo = Database::getConnection();
        
        $sql = "SELECT m.*, um.sender_id, um.receiver_id, u.pseudo as sender_name
                FROM message m
                INNER JOIN user_message um ON m.id = um.message_id
                INNER JOIN users u ON um.sender_id = u.id
                WHERE (um.sender_id = ? AND um.receiver_id = ?) 
                   OR (um.sender_id = ? AND um.receiver_id = ?)
                ORDER BY m.posted_at ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $friendId, $friendId, $userId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function sendMessage($senderId, $receiverId, $content)
    {
        $pdo = Database::getConnection();
        
        try {
            $pdo->beginTransaction();
            
            $sql = "INSERT INTO message (content, posted_at) VALUES (?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$content]);
            $messageId = $pdo->lastInsertId();
            
            $sql = "INSERT INTO user_message (message_id, sender_id, receiver_id) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$messageId, $senderId, $receiverId]);
            
            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Erreur envoi message: " . $e->getMessage());
            return false;
        }
    }
} 