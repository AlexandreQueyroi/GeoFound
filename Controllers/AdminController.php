<?php
namespace App\Controllers;

use App\Models\Permission;

class AdminController {
    public function index() {
        
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.access')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/index.php';
    }
    public function rank() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.ranks')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/rank.php';
    }
    public function user() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/user.php';
    }

    public function permissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.permissions')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/permissions.php';
    }

    public function maintenance() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.maintenance')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/maintenance.php';
    }

    
    public function apiPermissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.permissions')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $permissions = Permission::getAllPermissions();
                echo json_encode($permissions);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $name = $data['name'] ?? '';
                $description = $data['description'] ?? '';
                
                if (empty($name)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nom de permission requis']);
                    exit;
                }
                
                
                echo json_encode(['success' => true, 'message' => 'Permission créée']);
                break;
        }
    }

    public function apiRanks() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.ranks')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $ranks = Permission::getAllRanks();
                echo json_encode($ranks);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $name = $data['name'] ?? '';
                $color = $data['color'] ?? '';
                
                if (empty($name)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nom de rang requis']);
                    exit;
                }
                
                if (Permission::createRank($name, $color)) {
                    echo json_encode(['success' => true, 'message' => 'Rang créé']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la création du rang']);
                }
                break;
                
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                $name = $data['name'] ?? '';
                $color = $data['color'] ?? '';
                
                if (empty($id) || empty($name)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID et nom de rang requis']);
                    exit;
                }
                
                if (Permission::updateRank($id, $name, $color)) {
                    echo json_encode(['success' => true, 'message' => 'Rang mis à jour']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la mise à jour du rang']);
                }
                break;
                
            case 'DELETE':
                $id = $_GET['id'] ?? 0;
                
                if (empty($id)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de rang requis']);
                    exit;
                }
                
                if (Permission::deleteRank($id)) {
                    echo json_encode(['success' => true, 'message' => 'Rang supprimé']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la suppression du rang']);
                }
                break;
        }
    }

    public function apiRankPermissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.ranks')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $rankId = $_GET['rank_id'] ?? 0;
                if (empty($rankId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de rang requis']);
                    exit;
                }
                
                $permissions = Permission::getRankPermissions($rankId);
                echo json_encode($permissions);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $rankId = $data['rank_id'] ?? 0;
                $permissionId = $data['permission_id'] ?? 0;
                
                if (empty($rankId) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de rang et permission requis']);
                    exit;
                }
                
                if (Permission::addPermissionToRank($rankId, $permissionId)) {
                    echo json_encode(['success' => true, 'message' => 'Permission ajoutée au rang']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de l\'ajout de la permission']);
                }
                break;
                
            case 'DELETE':
                $rankId = $_GET['rank_id'] ?? 0;
                $permissionId = $_GET['permission_id'] ?? 0;
                
                if (empty($rankId) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de rang et permission requis']);
                    exit;
                }
                
                if (Permission::removePermissionFromRank($rankId, $permissionId)) {
                    echo json_encode(['success' => true, 'message' => 'Permission retirée du rang']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors du retrait de la permission']);
                }
                break;
        }
    }

    public function apiUserPermissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $userId = $_GET['user_id'] ?? 0;
                if (empty($userId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID d\'utilisateur requis']);
                    exit;
                }
                
                $permissions = Permission::getUserPermissions($userId);
                echo json_encode($permissions);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $userId = $data['user_id'] ?? 0;
                $permissionId = $data['permission_id'] ?? 0;
                $expiresAt = $data['expires_at'] ?? null;
                
                if (empty($userId) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID d\'utilisateur et permission requis']);
                    exit;
                }
                
                if (Permission::addPermissionToUser($userId, $permissionId, $expiresAt, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Permission ajoutée à l\'utilisateur']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de l\'ajout de la permission']);
                }
                break;
                
            case 'DELETE':
                $userId = $_GET['user_id'] ?? 0;
                $permissionId = $_GET['permission_id'] ?? 0;
                
                if (empty($userId) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID d\'utilisateur et permission requis']);
                    exit;
                }
                
                if (Permission::removePermissionFromUser($userId, $permissionId)) {
                    echo json_encode(['success' => true, 'message' => 'Permission retirée de l\'utilisateur']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors du retrait de la permission']);
                }
                break;
        }
    }

    public function apiMaintenance() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.maintenance')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $pagePath = $_GET['page_path'] ?? '';
                if (empty($pagePath)) {
                    $pages = Permission::getMaintenancePages();
                    echo json_encode($pages);
                } else {
                    $maintenance = Permission::getPageMaintenance($pagePath);
                    echo json_encode($maintenance);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $pagePath = $data['page_path'] ?? '';
                $pageName = $data['page_name'] ?? '';
                $isMaintenance = $data['is_maintenance'] ?? false;
                $message = $data['message'] ?? null;
                
                if (empty($pagePath) || empty($pageName)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Chemin et nom de page requis']);
                    exit;
                }
                
                if (Permission::setPageMaintenance($pagePath, $pageName, $isMaintenance, $message)) {
                    echo json_encode(['success' => true, 'message' => 'Maintenance mise à jour']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la mise à jour de la maintenance']);
                }
                break;
                
            case 'DELETE':
                $pagePath = $_GET['page_path'] ?? '';
                if (empty($pagePath)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Chemin de page requis']);
                    exit;
                }
                
                if (Permission::deletePageMaintenance($pagePath)) {
                    echo json_encode(['success' => true, 'message' => 'Maintenance supprimée']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la suppression de la maintenance']);
                }
                break;
        }
    }

    public function apiMaintenanceQuickAll() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.maintenance')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $isMaintenance = $data['is_maintenance'] ?? false;
            
            if (Permission::setAllPagesMaintenance($isMaintenance)) {
                echo json_encode(['success' => true, 'message' => 'Maintenance mise à jour pour toutes les pages']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la mise à jour de la maintenance']);
            }
        }
    }

    public function apiPagePermissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'page.permission.manage')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                
                $db = Permission::init();
                $stmt = $db->prepare("
                    SELECT pp.page_path, pp.permission_id, p.name as permission_name, p.description
                    FROM page_permissions pp
                    JOIN permissions p ON pp.permission_id = p.id
                    ORDER BY pp.page_path, p.name
                ");
                $stmt->execute();
                $pagePermissions = $stmt->fetchAll();
                echo json_encode($pagePermissions);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $pagePath = $data['page_path'] ?? '';
                $permissionId = $data['permission_id'] ?? '';
                $description = $data['description'] ?? '';
                
                if (empty($pagePath) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Chemin de page et permission requis']);
                    exit;
                }
                
                if (Permission::addPagePermission($pagePath, $permissionId)) {
                    echo json_encode(['success' => true, 'message' => 'Permission ajoutée à la page']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de l\'ajout de la permission']);
                }
                break;
                
            case 'DELETE':
                $pagePath = $_GET['page_path'] ?? '';
                $permissionId = $_GET['permission_id'] ?? 0;
                
                if (empty($pagePath) || empty($permissionId)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Chemin de page et ID de permission requis']);
                    exit;
                }
                
                if (Permission::removePagePermission($pagePath, $permissionId)) {
                    echo json_encode(['success' => true, 'message' => 'Permission retirée de la page']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors du retrait de la permission']);
                }
                break;
        }
    }

    public function apiPagePermissionsClear() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'page.permission.manage')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $pagePath = $_GET['page_path'] ?? '';
            
            if (empty($pagePath)) {
                http_response_code(400);
                echo json_encode(['error' => 'Chemin de page requis']);
                exit;
            }
            
            if (Permission::clearPagePermissions($pagePath)) {
                echo json_encode(['success' => true, 'message' => 'Toutes les permissions de la page ont été supprimées']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la suppression des permissions']);
            }
        }
    }
} 