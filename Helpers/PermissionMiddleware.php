<?php
namespace App\Helpers;

use App\Models\Permission;

class PermissionMiddleware {
    
    
    public static function checkPageAccess($pagePath) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['last_url'] = $_SERVER['REQUEST_URI'];
            return;
        }
        
        if (!Permission::canAccessPage($_SESSION['user_id'], $pagePath)) {
            header('Location: /error/403');
            exit;
        }
        
        return true;
    }
    
    
    public static function requirePermission($permissionName) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /error/403');
            exit;
        }
        
        if (!Permission::hasPermission($_SESSION['user_id'], $permissionName)) {
            header('Location: /error/403');
            exit;
        }
        
        return true;
    }
    
    
    public static function requireAdmin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /error/403');
            exit;
        }
        
        if (!Permission::isAdmin($_SESSION['user_id'])) {
            header('Location: /error/403');
            exit;
        }
        
        return true;
    }
    
    
    public static function checkMaintenance($pagePath = null) {
        if (!$pagePath) {
            $pagePath = $_SERVER['REQUEST_URI'];
        }
        
        
        $maintenance = Permission::getPageMaintenance($pagePath);
        
        if ($maintenance && $maintenance['is_maintenance']) {
            
            if (isset($_SESSION['user_id']) && Permission::hasPermission($_SESSION['user_id'], '*')) {
                
                return true;
            }
            
            
            if (isset($_COOKIE['bypass_maintenance']) && $_COOKIE['bypass_maintenance'] === '1') {
                return true;
            }
            
            
            http_response_code(503);
            require __DIR__ . '/../Views/error/maintenance.php';
            exit;
        }
        
        return true;
    }
    
    
    public static function getUserPermissions() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        return Permission::getUserPermissions($_SESSION['user_id']);
    }
    
    
    public static function canManagePagePermissions() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        return Permission::hasPermission($_SESSION['user_id'], 'page.permission.manage');
    }
    
    
    public static function canBypassMaintenance() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        return Permission::hasPermission($_SESSION['user_id'], '*');
    }
    
    
    public static function canManageMaintenance() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        return Permission::hasPermission($_SESSION['user_id'], 'admin.maintenance');
    }
} 