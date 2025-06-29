<?php
namespace App\Models;

use App\Helpers\Database;

class Rank {
    
    /**
     * Récupère tous les grades
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM ranks ORDER BY priority DESC, name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un grade par son nom
     */
    public static function getByName($name) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM ranks WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère un grade par son ID
     */
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM ranks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Crée un nouveau grade
     */
    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO ranks (name, display_name, color, background_color, priority, description, permissions) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['display_name'],
            $data['color'],
            $data['background_color'],
            $data['priority'],
            $data['description'],
            $data['permissions'] ?? null
        ]);
        return $db->lastInsertId();
    }
    
    /**
     * Met à jour un grade
     */
    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE ranks 
            SET name = ?, display_name = ?, color = ?, background_color = ?, priority = ?, description = ?, permissions = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['display_name'],
            $data['color'],
            $data['background_color'],
            $data['priority'],
            $data['description'],
            $data['permissions'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprime un grade
     */
    public static function delete($id) {
        $db = Database::getConnection();
        
        // Vérifier si des utilisateurs utilisent ce grade
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE user_rank = (SELECT name FROM ranks WHERE id = ?)");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            throw new \Exception("Impossible de supprimer ce grade car des utilisateurs l'utilisent encore.");
        }
        
        $stmt = $db->prepare("DELETE FROM ranks WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Récupère les permissions d'un grade
     */
    public static function getPermissions($rankName) {
        $rank = self::getByName($rankName);
        if (!$rank || !$rank['permissions']) {
            return [];
        }
        return json_decode($rank['permissions'], true) ?: [];
    }
    
    /**
     * Vérifie si un grade a une permission spécifique
     */
    public static function hasPermission($rankName, $permission) {
        $permissions = self::getPermissions($rankName);
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }
    
    /**
     * Récupère le grade d'un utilisateur avec ses informations
     */
    public static function getUserRank($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.* FROM ranks r 
            JOIN users u ON u.user_rank = r.name 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Met à jour le grade d'un utilisateur
     */
    public static function setUserRank($userId, $rankName) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET user_rank = ? WHERE id = ?");
        return $stmt->execute([$rankName, $userId]);
    }
    
    /**
     * Récupère les statistiques des grades
     */
    public static function getStats() {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.name, r.display_name, r.color, COUNT(u.id) as user_count
            FROM ranks r
            LEFT JOIN users u ON u.user_rank = r.name
            GROUP BY r.id, r.name, r.display_name, r.color
            ORDER BY r.priority DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 