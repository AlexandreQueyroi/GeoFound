<?php
namespace App\Models;

use App\Helpers\Database;

class Reward {
    
    /**
     * Récupère toutes les récompenses
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM rewards ORDER BY required_level ASC, name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une récompense par son ID
     */
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM rewards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère les récompenses d'un utilisateur
     */
    public static function getUserRewards($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, ur.unlocked_at, ur.is_equipped
            FROM user_rewards ur
            JOIN rewards r ON ur.reward_id = r.id
            WHERE ur.user_id = ?
            ORDER BY ur.unlocked_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si un utilisateur a une récompense
     */
    public static function userHasReward($userId, $rewardId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM user_rewards WHERE user_id = ? AND reward_id = ?");
        $stmt->execute([$userId, $rewardId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Débloque une récompense pour un utilisateur
     */
    public static function unlockReward($userId, $rewardId) {
        $db = Database::getConnection();
        
        // Vérifier si la récompense n'est pas déjà débloquée
        if (self::userHasReward($userId, $rewardId)) {
            return false;
        }
        
        // Vérifier le stock pour les récompenses physiques
        $reward = self::getById($rewardId);
        if ($reward && $reward['type'] === 'physical') {
            if ($reward['stock'] <= 0) {
                return false; // Plus de stock disponible
            }
            
            // Décrémenter le stock
            $stmt = $db->prepare("UPDATE rewards SET stock = stock - 1 WHERE id = ? AND stock > 0");
            $stmt->execute([$rewardId]);
            
            if ($stmt->rowCount() === 0) {
                return false; // Échec de la mise à jour du stock
            }
        }
        
        $stmt = $db->prepare("INSERT INTO user_rewards (user_id, reward_id, unlocked_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$userId, $rewardId]);
    }
    
    /**
     * Équipe/déséquipe une récompense
     */
    public static function toggleEquip($userId, $rewardId) {
        $db = Database::getConnection();
        
        // Vérifier si l'utilisateur a la récompense
        if (!self::userHasReward($userId, $rewardId)) {
            return false;
        }
        
        // Déséquiper toutes les récompenses du même type
        $reward = self::getById($rewardId);
        if ($reward) {
            $stmt = $db->prepare("
                UPDATE user_rewards ur
                JOIN rewards r ON ur.reward_id = r.id
                SET ur.is_equipped = 0
                WHERE ur.user_id = ? AND r.type = ? AND ur.reward_id != ?
            ");
            $stmt->execute([$userId, $reward['type'], $rewardId]);
        }
        
        // Basculer l'état d'équipement de la récompense actuelle
        $stmt = $db->prepare("
            UPDATE user_rewards 
            SET is_equipped = NOT is_equipped 
            WHERE user_id = ? AND reward_id = ?
        ");
        return $stmt->execute([$userId, $rewardId]);
    }
    
    /**
     * Récupère les récompenses disponibles pour un utilisateur selon son niveau
     */
    public static function getAvailableRewards($userLevel) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM rewards 
            WHERE required_level <= ? 
            AND (type != 'physical' OR stock > 0)
            ORDER BY required_level ASC, name ASC
        ");
        $stmt->execute([$userLevel]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les statistiques des récompenses
     */
    public static function getStats() {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                r.name,
                r.type,
                r.required_level,
                COUNT(ur.user_id) as unlock_count,
                (SELECT COUNT(*) FROM users) as total_users
            FROM rewards r
            LEFT JOIN user_rewards ur ON r.id = ur.reward_id
            GROUP BY r.id, r.name, r.type, r.required_level
            ORDER BY r.required_level ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crée une nouvelle récompense (admin)
     */
    public static function create($data) {
        $db = Database::getConnection();
        
        $sql = "
            INSERT INTO rewards (name, description, type, icon, required_level, rarity, points_value, price, stock) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['type'],
            $data['icon'],
            $data['required_level'],
            $data['rarity'],
            $data['points_value'],
            $data['price'] ?? null,
            $data['stock'] ?? null
        ]);
        return $db->lastInsertId();
    }
    
    /**
     * Met à jour une récompense (admin)
     */
    public static function update($id, $data) {
        $db = Database::getConnection();
        
        $sql = "
            UPDATE rewards 
            SET name = ?, description = ?, type = ?, icon = ?, required_level = ?, 
                rarity = ?, points_value = ?, price = ?, stock = ?
            WHERE id = ?
        ";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['type'],
            $data['icon'],
            $data['required_level'],
            $data['rarity'],
            $data['points_value'],
            $data['price'] ?? null,
            $data['stock'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprime une récompense (admin)
     */
    public static function delete($id) {
        $db = Database::getConnection();
        
        // Supprimer d'abord les associations utilisateur
        $stmt = $db->prepare("DELETE FROM user_rewards WHERE reward_id = ?");
        $stmt->execute([$id]);
        
        // Puis supprimer la récompense
        $stmt = $db->prepare("DELETE FROM rewards WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Récupère les récompenses physiques avec stock
     */
    public static function getPhysicalRewards() {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, COUNT(ur.user_id) as unlock_count
            FROM rewards r
            LEFT JOIN user_rewards ur ON r.id = ur.reward_id
            WHERE r.type = 'physical'
            GROUP BY r.id
            ORDER BY r.required_level ASC, r.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie la disponibilité d'une récompense physique
     */
    public static function isPhysicalRewardAvailable($rewardId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT stock FROM rewards WHERE id = ? AND type = 'physical'");
        $stmt->execute([$rewardId]);
        $stock = $stmt->fetchColumn();
        return $stock > 0;
    }
} 