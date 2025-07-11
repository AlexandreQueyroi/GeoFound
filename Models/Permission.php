<?php
namespace App\Models;

use App\Helpers\Database;

class Permission {
    private static $db;

    public static function init() {
        self::$db = Database::getConnection();
    }

    public static function hasPermission($userId, $permissionName) {
        self::init();
        
        
        $stmt = self::$db->prepare("
            SELECT p.name 
            FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND (up.expires_at IS NULL OR up.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        $userPermissions = $stmt->fetchAll();
        
        foreach ($userPermissions as $perm) {
            if ($perm['name'] === $permissionName || $perm['name'] === '*') {
                return true;
            }
        }
        
        
        $stmt = self::$db->prepare("
            SELECT r.permissions 
            FROM users u
            JOIN ranks r ON u.user_rank = r.name
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $rank = $stmt->fetch();
        
        if ($rank && $rank['permissions']) {
            $rankPermissions = json_decode($rank['permissions'], true);
            if (is_array($rankPermissions)) {
                if (in_array($permissionName, $rankPermissions) || in_array('*', $rankPermissions)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    public static function isAdmin($userId) {
        return self::hasPermission($userId, '*');
    }

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
            JOIN ranks r ON rp.rank_id = r.id
            JOIN users u ON u.user_rank = r.name
            WHERE pp.page_path = ? AND u.id = ?
        ");
        $stmt->execute([$pagePath, $userId]);
        return $stmt->fetch() !== false;
    }

    public static function getAllPermissions() {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM permissions ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAllRanks() {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM ranks ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

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
        $userPermissions = $stmt->fetchAll();
        
        
        $stmt = self::$db->prepare("
            SELECT r.permissions 
            FROM users u
            JOIN ranks r ON u.user_rank = r.name
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $rank = $stmt->fetch();
        
        $rankPermissions = [];
        if ($rank && $rank['permissions']) {
            $permissionNames = json_decode($rank['permissions'], true);
            if (is_array($permissionNames)) {
                foreach ($permissionNames as $permName) {
                    
                    $stmt = self::$db->prepare("SELECT * FROM permissions WHERE name = ?");
                    $stmt->execute([$permName]);
                    $perm = $stmt->fetch();
                    if ($perm) {
                        $perm['granted_at'] = null; 
                        $perm['expires_at'] = null; 
                        $rankPermissions[] = $perm;
                    }
                }
            }
        }
        
        
        return array_merge($userPermissions, $rankPermissions);
    }

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

    public static function removePermissionFromRank($rankId, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM rank_permissions 
            WHERE rank_id = ? AND permission_id = ?
        ");
        return $stmt->execute([$rankId, $permissionId]);
    }

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

    public static function removePermissionFromUser($userId, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM user_permissions 
            WHERE user_id = ? AND permission_id = ?
        ");
        return $stmt->execute([$userId, $permissionId]);
    }

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

    public static function updateRank($rankId, $name, $color) {
        self::init();
        $stmt = self::$db->prepare("
            UPDATE ranks SET name = ?, color = ? WHERE id = ?
        ");
        return $stmt->execute([$name, $color, $rankId]);
    }

    public static function deleteRank($rankId) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM ranks WHERE id = ?");
        return $stmt->execute([$rankId]);
    }

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

    public static function getPageMaintenance($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT * FROM page_maintenance WHERE page_path = ?
        ");
        $stmt->execute([$pagePath]);
        return $stmt->fetch();
    }

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

    public static function removePagePermission($pagePath, $permissionId) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_permissions 
            WHERE page_path = ? AND permission_id = ?
        ");
        return $stmt->execute([$pagePath, $permissionId]);
    }

    public static function getMaintenancePages() {
        self::init();
        try {
            $stmt = self::$db->prepare("
                SELECT 
                    page_path,
                    page_name,
                    is_maintenance,
                    maintenance_message,
                    created_at,
                    updated_at
                FROM page_maintenance
                ORDER BY page_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function deletePageMaintenance($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_maintenance WHERE page_path = ?
        ");
        return $stmt->execute([$pagePath]);
    }

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

    public static function clearPagePermissions($pagePath) {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM page_permissions WHERE page_path = ?
        ");
        return $stmt->execute([$pagePath]);
    }
    public static function cleanExpiredPermissions() {
        self::init();
        $stmt = self::$db->prepare("
            DELETE FROM user_permissions 
            WHERE expires_at IS NOT NULL AND expires_at < NOW()
        ");
        return $stmt->execute();
    }
} 