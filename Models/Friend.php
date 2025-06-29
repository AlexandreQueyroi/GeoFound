<?php
namespace App\Models;
use App\Helpers\Database;

class Friend {
    public static function getFriends($userId) {
        $pdo = Database::getConnection();
        
        $sql = "SELECT u.id, u.pseudo, u.email, u.connected, u.avatar_id
                FROM users u
                WHERE u.id IN (
                    SELECT user2_id FROM follow WHERE user1_id = :userId AND state = 'accepted'
                    UNION
                    SELECT user1_id FROM follow WHERE user2_id = :userId AND state = 'accepted'
                )
                ORDER BY u.pseudo ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        
        $friends = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($friends as &$friend) {
            $isOnline = false;
            if (!empty($friend['connected'])) {
                $lastSeen = new \DateTime($friend['connected']);
                $now = new \DateTime();
                $interval = $now->getTimestamp() - $lastSeen->getTimestamp();
                if ($interval < 300) {
                    $isOnline = true;
                }
            }
            $friend['is_online'] = $isOnline;
        }
        
        return $friends;
    }

    public static function getFriendsWithUnread($userId) {
        $pdo = Database::getConnection();
        $sql = 'SELECT u.id, u.pseudo, u.avatar_id,
            (SELECT COUNT(*) FROM message m 
            JOIN user_message um ON m.id = um.message_id 
            WHERE um.sender_id = u.id AND um.receiver_id = :uid AND m.state = "sent") as unread_count
            FROM users u
            JOIN follow f ON ((f.user1_id = :uid AND f.user2_id = u.id)
            OR (f.user2_id = :uid AND f.user1_id = u.id))
            WHERE f.state = "accepted"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function areFriends($userId1, $userId2) {
        $pdo = Database::getConnection();
        
        $sql = "SELECT COUNT(*) as count
                FROM follow
                WHERE ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))
                AND state = 'accepted'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public static function getRequests($userId) {
        $pdo = Database::getConnection();
        $sqlRec = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user1_id=u.id WHERE f.user2_id=:uid AND f.state='pending'";
        $stmtRec = $pdo->prepare($sqlRec);
        $stmtRec->execute(['uid'=>$userId]);
        $received = $stmtRec->fetchAll(\PDO::FETCH_ASSOC);
        
        $sqlSent = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user2_id=u.id WHERE f.user1_id=:uid AND f.state='pending'";
        $stmtSent = $pdo->prepare($sqlSent);
        $stmtSent->execute(['uid'=>$userId]);
        $sent = $stmtSent->fetchAll(\PDO::FETCH_ASSOC);
        
        return ['received'=>$received, 'sent'=>$sent];
    }

    public static function addFriend($userId, $pseudo) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE pseudo = ?');
        $stmt->execute([$pseudo]);
        $user = $stmt->fetch();
        if (!$user) return 'Utilisateur introuvable';
        
        $targetId = $user['id'];
        if ($targetId == $userId) return 'Impossible de s\'ajouter soi-même';
        
        $stmt = $pdo->prepare('SELECT * FROM follow WHERE (user1_id=? AND user2_id=?) OR (user1_id=? AND user2_id=?)');
        $stmt->execute([$userId, $targetId, $targetId, $userId]);
        if ($stmt->rowCount() > 0) return 'Demande déjà envoyée';
        
        $stmt = $pdo->prepare('INSERT INTO follow (user1_id, user2_id, follow_at, state) VALUES (?, ?, NOW(), "pending")');
        $stmt->execute([$userId, $targetId]);
        return 'Demande envoyée';
    }

    public static function acceptFriend($userId, $id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE follow SET state="accepted" WHERE id=? AND user2_id=?');
        $stmt->execute([$id, $userId]);
        return 'Ami accepté';
    }

    public static function refuseFriend($userId, $id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM follow WHERE id=? AND user2_id=?');
        $stmt->execute([$id, $userId]);
        return 'Demande refusée';
    }

    public static function getPendingRequests($userId)
    {
        $pdo = Database::getConnection();
        
        $sql = "SELECT u.id, u.pseudo, u.email, f.follow_at
                FROM users u
                INNER JOIN follow f ON f.user1_id = u.id AND f.user2_id = ?
                WHERE f.state = 'pending'
                ORDER BY f.follow_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 