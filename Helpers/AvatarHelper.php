<?php
namespace App\Helpers;

use App\Helpers\Database;

class AvatarHelper {
    
    public static function getAvatarUrl($avatarId) {
        if (!$avatarId) {
            return '/assets/img/avatars/default-avatar.png';
        }
        
        
        
        return '/assets/img/avatars/default-avatar.png';
    }
    
    public static function getAvatarForUser($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT avatar_id FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result && $result['avatar_id']) {
            return self::getAvatarUrl($result['avatar_id']);
        }
        
        return '/assets/img/avatars/default-avatar.png';
    }
    
    public static function getInitials($pseudo) {
        return strtoupper(substr($pseudo, 0, 1));
    }
} 