<?php
namespace App\Models;

use App\Helpers\Database;

class Permission {
    private static $db;

    public static function init() {
        self::$db = Database::getConnection();
    }

    /**
     * Vérifie si un utilisateur a une permission spécifique
     */
    public static function hasPermission($userId, $permissionName) {
        self::init();
        
        
        $stmt = self::$db->prepare("
            SELECT 1 FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND p.name = ? 
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
        ");
        $stmt->execute([$userId, $permissionName]);
        if ($stmt->fetch()) {
            return true;
        }

        
        $stmt = self::$db->prepare("
            SELECT 1 FROM users u
            JOIN ranks r ON u.rank = r.id
            JOIN rank_permissions rp ON r.id = rp.rank_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE u.id = ? AND p.name = ?
        ");
        $stmt->execute([$userId, $permissionName]);
        return $stmt->fetch() !== false;
    }

    /**
     * Vérifie si un utilisateur a la permission administrateur (*)
     */
    public static function isAdmin($userId) {
        return self::hasPermission($userId, '*');
    }

    /**
     * Vérifie si un utilisateur peut accéder à une page spécifique
     */
    public static function canAccessPage($userId, $pagePath) {
        
        if (self::isAdmin($userId)) {
            return true;
        }

        
        $stmt = self::$db->prepare("
            SELECT is_maintenance FROM page_maintenance 
            WHERE page_path = ?
        ");
        $stmt->execute([$pagePath]);
        $maintenance = $stmt->fetch();

        if ($maintenance && $maintenance['is_maintenance']) {
            
            return self::hasPermission($userId, 'page.maintenance.bypass');
        }

        
        $stmt = self::$db->prepare("
            SELECT 1 FROM page_permissions pp
            JOIN permissions p ON pp.permission_id = p.id
            JOIN user_permissions up ON p.id = up.permission_id
            WHERE pp.page_path = ? AND up.user_id = ?
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
        ");
        $stmt->execute([$pagePath, $userId]);
        if ($stmt->fetch()) {
            return true;
        }

        
        $stmt = self::$db->prepare("
            SELECT 1 FROM page_permissions pp
            JOIN permissions p ON pp.permission_id = p.id
            JOIN rank_permissions rp ON p.id = rp.permission_id
            JOIN users u ON rp.rank_id = u.rank
            WHERE pp.page_path = ? AND u.id = ?
        ");
        $stmt->execute([$pagePath, $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Récupère toutes les permissions
     */
    public static function getAllPermissions() {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM permissions ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les rangs
     */
    public static function getAllRanks() {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM ranks ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les permissions d'un rang
     */
    public static function getRankPermissions($rankId) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT p.* FROM permissions p
            JOIN rank_permissions rp ON p.id = rp.permission_id
            WHERE rp.rank_id = ?
        ");
        $stmt->execute([$rankId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les permissions d'un utilisateur
     */
    public static function getUserPermissions($userId) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT p.*, up.expires_at, up.created_at as granted_at
            FROM permissions p
            JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = ?
            ORDER BY p.name
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute une permission à un rang
     */
    public static function addPermissionToRank($rankId, $permissionId) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                INSERT INTO rank_permissions (rank_id, permission_id) 
                VALUES (?, ?)
            ");
            return $stmt->execute([$rankId, $permissionId]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime une permission d'un rang
     */
    public static function removePermissionFromRank($rankId, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM rank_permissions 
            WHERE rank_id = ? AND permission_id = ?
        ");
        return $stmt->execute([$rankId, $permissionId]);
    }

    /**
     * Ajoute une permission temporaire à un utilisateur
     */
    public static function addPermissionToUser($userId, $permissionId, $expiresAt = null, $createdBy = null) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                INSERT INTO user_permissions (user_id, permission_id, expires_at, created_by) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE expires_at = VALUES(expires_at)
            ");
            return $stmt->execute([$userId, $permissionId, $expiresAt, $createdBy]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime une permission d'un utilisateur
     */
    public static function removePermissionFromUser($userId, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM user_permissions 
            WHERE user_id = ? AND permission_id = ?
        ");
        return $stmt->execute([$userId, $permissionId]);
    }

    /**
     * Crée un nouveau rang
     */
    public static function createRank($name, $color) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                INSERT INTO ranks (name, color) VALUES (?, ?)
            ");
            return $stmt->execute([$name, $color]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour un rang
     */
    public static function updateRank($rankId, $name, $color) {
        self::init();
        $stmt = self::$db->prepare("
            UPDATE ranks SET name = ?, color = ? WHERE id = ?
        ");
        return $stmt->execute([$name, $color, $rankId]);
    }

    /**
     * Supprime un rang
     */
    public static function deleteRank($rankId) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM ranks WHERE id = ?");
        return $stmt->execute([$rankId]);
    }

    /**
     * Gère la maintenance d'une page
     */
    public static function setPageMaintenance($pagePath, $pageName, $isMaintenance, $message = null) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                INSERT INTO page_maintenance (page_path, page_name, is_maintenance, maintenance_message) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                is_maintenance = VALUES(is_maintenance),
                maintenance_message = VALUES(maintenance_message),
                updated_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([$pagePath, $pageName, $isMaintenance, $message]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère l'état de maintenance d'une page
     */
    public static function getPageMaintenance($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT * FROM page_maintenance WHERE page_path = ?
        ");
        $stmt->execute([$pagePath]);
        return $stmt->fetch();
    }

    /**
     * Ajoute une permission requise pour une page
     */
    public static function addPagePermission($pagePath, $permissionId) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                INSERT INTO page_permissions (page_path, permission_id) 
                VALUES (?, ?)
            ");
            return $stmt->execute([$pagePath, $permissionId]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime une permission d'une page
     */
    public static function removePagePermission($pagePath, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_permissions 
            WHERE page_path = ? AND permission_id = ?
        ");
        return $stmt->execute([$pagePath, $permissionId]);
    }

    /**
     * Récupère toutes les pages en maintenance
     */
    public static function getMaintenancePages() {
        self::init();
        $stmt = self::$db->prepare("
            SELECT 
                all_pages.page_path,
                COALESCE(pm.page_name, all_pages.page_path) AS page_name,
                COALESCE(pm.is_maintenance, 0) AS is_maintenance,
                pm.maintenance_message,
                pm.created_at,
                pm.updated_at
            FROM (
                SELECT page_path FROM page_maintenance
                UNION
                SELECT page_path FROM page_permissions
            ) AS all_pages
            LEFT JOIN page_maintenance pm ON all_pages.page_path = pm.page_path
            ORDER BY page_name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Supprime la maintenance d'une page
     */
    public static function deletePageMaintenance($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_maintenance WHERE page_path = ?
        ");
        return $stmt->execute([$pagePath]);
    }

    /**
     * Met toutes les pages en maintenance ou les désactive
     */
    public static function setAllPagesMaintenance($isMaintenance) {
        self::init();
        try {
            $stmt = self::$db->prepare("
                UPDATE page_maintenance SET is_maintenance = ?, updated_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([$isMaintenance]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime toutes les permissions d'une page
     */
    public static function clearPagePermissions($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_permissions WHERE page_path = ?
        ");
        return $stmt->execute([$pagePath]);
    }

    /**
     * Nettoie les permissions expirées
     */
    public static function cleanExpiredPermissions() {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM user_permissions 
            WHERE expires_at IS NOT NULL AND expires_at < NOW()
        ");
        return $stmt->execute();
    }
} 