<?php
namespace App\Models;

use App\Helpers\Database;

class EmailVerification {
    private static $db;
    
    public static function init() {
        if (!self::$db) {
            self::$db = Database::getConnection();
        }
    }
    
    public static function generateToken($userId) {
        self::init();
        
        
        $stmt = self::$db->prepare("DELETE FROM email_verification_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = self::$db->prepare("
            INSERT INTO email_verification_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $token, $expiresAt]);
        
        return $token;
    }
    
    public static function validateToken($token) {
        self::init();
        
        $stmt = self::$db->prepare("
            SELECT user_id, expires_at 
            FROM email_verification_tokens 
            WHERE token = ? AND expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        
        $stmt = self::$db->prepare("UPDATE users SET email_verified = TRUE WHERE id = ?");
        $stmt->execute([$result['user_id']]);
        
        
        $stmt = self::$db->prepare("DELETE FROM email_verification_tokens WHERE token = ?");
        $stmt->execute([$token]);
        
        return $result['user_id'];
    }
    
    public static function hasValidToken($userId) {
        self::init();
        
        $stmt = self::$db->prepare("
            SELECT COUNT(*) FROM email_verification_tokens 
            WHERE user_id = ? AND expires_at > NOW()
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    public static function cleanupExpiredTokens() {
        self::init();
        
        $stmt = self::$db->prepare("DELETE FROM email_verification_tokens WHERE expires_at <= NOW()");
        $stmt->execute();
    }
} 