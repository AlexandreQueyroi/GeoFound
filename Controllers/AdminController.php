<?php
namespace App\Controllers;

use App\Models\Permission;
use App\Helpers\Database;
use PDO;
use Exception;

class AdminController {
    public function index() {
        // Vérification des permissions d'admin
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.access')) {
            header('Location: /error/403');
            exit;
        }
        
        // Récupérer les vraies permissions de l'utilisateur
        $permissions = Permission::getUserPermissions($_SESSION['user_id']);
        $userPermissions = array_map(function($perm) {
            return $perm['name'];
        }, $permissions);
        
        // Ajouter les permissions du rang si l'utilisateur en a un
        if (isset($_SESSION['user_id'])) {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT p.name FROM permissions p
                JOIN rank_permissions rp ON p.id = rp.permission_id
                JOIN users u ON rp.rank_id = u.user_rank
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $rankPermissions = $stmt->fetchAll();
            foreach ($rankPermissions as $perm) {
                if (!in_array($perm['name'], $userPermissions)) {
                    $userPermissions[] = $perm['name'];
                }
            }
        }
        
        require __DIR__ . '/../Views/admin/index.php';
    }
    public function rank() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        
        if (!Permission::hasPermission($_SESSION['user_id'], 'admin.rank')) {
            header('Location: /');
            exit;
        }
        
        $db = Database::getConnection();
        
        // Traitement des actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            try {
                switch ($action) {
                    case 'create_rank':
                        $rankData = [
                            'name' => trim($_POST['name']),
                            'display_name' => trim($_POST['display_name']),
                            'color' => $_POST['color'],
                            'background_color' => $_POST['background_color'],
                            'priority' => intval($_POST['priority']),
                            'description' => trim($_POST['description']),
                            'permissions' => isset($_POST['permissions']) ? json_encode($_POST['permissions']) : null
                        ];
                        
                        // Validation
                        if (empty($rankData['name']) || empty($rankData['display_name'])) {
                            throw new \Exception("Le nom et le nom d'affichage sont requis.");
                        }
                        
                        \App\Models\Rank::create($rankData);
                        $_SESSION['admin_success'] = "Grade créé avec succès.";
                        break;
                        
                    case 'update_rank':
                        $rankId = intval($_POST['rank_id']);
                        $rankData = [
                            'name' => trim($_POST['name']),
                            'display_name' => trim($_POST['display_name']),
                            'color' => $_POST['color'],
                            'background_color' => $_POST['background_color'],
                            'priority' => intval($_POST['priority']),
                            'description' => trim($_POST['description']),
                            'permissions' => isset($_POST['permissions']) ? json_encode($_POST['permissions']) : null
                        ];
                        
                        \App\Models\Rank::update($rankId, $rankData);
                        $_SESSION['admin_success'] = "Grade mis à jour avec succès.";
                        break;
                        
                    case 'delete_rank':
                        $rankId = intval($_POST['rank_id']);
                        \App\Models\Rank::delete($rankId);
                        $_SESSION['admin_success'] = "Grade supprimé avec succès.";
                        break;
                        
                    case 'update_user_rank':
                        $userId = intval($_POST['user_id']);
                        $newRank = trim($_POST['rank']);
                        
                        if (!empty($newRank)) {
                            \App\Models\Rank::setUserRank($userId, $newRank);
                            $_SESSION['admin_success'] = "Grade de l'utilisateur mis à jour avec succès.";
                        }
                        break;
                }
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = $e->getMessage();
            }
            
            header('Location: /admin/rank');
            exit;
        }
        
        // Récupération des données
        $ranks = \App\Models\Rank::getAll();
        $rankStats = \App\Models\Rank::getStats();
        
        // Récupération des utilisateurs avec leurs grades
        $stmt = $db->prepare("
            SELECT u.id, u.pseudo, u.email, u.user_rank, u.connected, u.desactivated, u.email_verified,
                   r.display_name as rank_display_name, r.color as rank_color, r.background_color as rank_bg_color
            FROM users u
            LEFT JOIN ranks r ON u.user_rank = r.name
            ORDER BY r.priority DESC, u.pseudo ASC
        ");
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        // Récupération des permissions disponibles
        $stmt = $db->prepare("SELECT id, name, description FROM permissions ORDER BY name");
        $stmt->execute();
        $availablePermissions = $stmt->fetchAll();
        
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
        
        // Headers pour éviter le cache
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        require __DIR__ . '/../Views/admin/maintenance.php';
    }

    public function users() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/users.php';
    }

    
    public function apiPermissions() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.permissions')) {
            header('Location: /error/403');
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
                $ranks = \App\Models\Rank::getAll();
                echo json_encode($ranks);
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                
                try {
                    $rankData = [
                        'name' => $data['name'] ?? '',
                        'display_name' => $data['display_name'] ?? '',
                        'color' => $data['color'] ?? '#3B82F6',
                        'background_color' => $data['background_color'] ?? '#1E40AF',
                        'priority' => intval($data['priority'] ?? 10),
                        'description' => $data['description'] ?? '',
                        'permissions' => isset($data['permissions']) ? json_encode($data['permissions']) : null
                    ];
                    
                    if (empty($rankData['name']) || empty($rankData['display_name'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Nom et nom d\'affichage requis']);
                        exit;
                    }
                    
                    \App\Models\Rank::create($rankData);
                    echo json_encode(['success' => true, 'message' => 'Grade créé']);
                } catch (\Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la création du grade: ' . $e->getMessage()]);
                }
                break;
                
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                
                try {
                    $rankData = [
                        'name' => $data['name'] ?? '',
                        'display_name' => $data['display_name'] ?? '',
                        'color' => $data['color'] ?? '#3B82F6',
                        'background_color' => $data['background_color'] ?? '#1E40AF',
                        'priority' => intval($data['priority'] ?? 10),
                        'description' => $data['description'] ?? '',
                        'permissions' => isset($data['permissions']) ? json_encode($data['permissions']) : null
                    ];
                    
                    if (empty($id) || empty($rankData['name']) || empty($rankData['display_name'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID, nom et nom d\'affichage requis']);
                        exit;
                    }
                    
                    \App\Models\Rank::update($id, $rankData);
                    echo json_encode(['success' => true, 'message' => 'Grade mis à jour']);
                } catch (\Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la mise à jour du grade: ' . $e->getMessage()]);
                }
                break;
                
            case 'DELETE':
                $id = $_GET['id'] ?? 0;
                
                try {
                    if (empty($id)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de grade requis']);
                        exit;
                    }
                    
                    \App\Models\Rank::delete($id);
                    echo json_encode(['success' => true, 'message' => 'Grade supprimé']);
                } catch (\Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la suppression du grade: ' . $e->getMessage()]);
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
            header('Location: /error/403');
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
                // DEBUG LOG
                $rawInput = file_get_contents('php://input');
                error_log('DEBUG POST RAW: ' . $rawInput);
                $data = json_decode($rawInput, true);
                error_log('DEBUG POST DECODE: ' . print_r($data, true));
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
                
                Permission::init();
                $stmt = Permission::$db->prepare("
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

    // API pour les statistiques du tableau de bord
    public function apiStats() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.access')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        try {
            $db = \App\Helpers\Database::getConnection();
            
            // Statistiques des utilisateurs
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
            $stmt->execute();
            $users = $stmt->fetch()['count'];
            
            // Statistiques des posts (vérifier si la table existe)
            $posts = 0;
            try {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM post");
                $stmt->execute();
                $posts = $stmt->fetch()['count'];
            } catch (\Exception $e) {
                // Table post n'existe pas, on garde 0
            }
            
            // Statistiques des messages (vérifier si la table existe)
            $messages = 0;
            try {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM messages");
                $stmt->execute();
                $messages = $stmt->fetch()['count'];
            } catch (\Exception $e) {
                // Table messages n'existe pas, on garde 0
            }
            
            // Pages en maintenance (vérifier si la table existe)
            $maintenance = 0;
            try {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM page_maintenance WHERE is_maintenance = 1");
                $stmt->execute();
                $maintenance = $stmt->fetch()['count'];
            } catch (\Exception $e) {
                // Table page_maintenance n'existe pas, on garde 0
            }
            
            echo json_encode([
                'users' => $users,
                'posts' => $posts,
                'messages' => $messages,
                'maintenance' => $maintenance
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la récupération des statistiques', 'details' => $e->getMessage()]);
        }
    }

    // API pour l'activité récente
    public function apiActivity() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.access')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        try {
            $db = \App\Helpers\Database::getConnection();
            
            // Récupérer les dernières activités (exemple avec les derniers utilisateurs connectés)
            $stmt = $db->prepare("
                SELECT u.username, u.connected, 'Connexion utilisateur' as description
                FROM users u 
                WHERE u.connected IS NOT NULL 
                ORDER BY u.connected DESC 
                LIMIT 5
            ");
            $stmt->execute();
            $activities = $stmt->fetchAll();
            
            // Formater les données
            $formattedActivities = array_map(function($activity) {
                return [
                    'description' => $activity['description'] . ' : ' . $activity['username'],
                    'created_at' => $activity['connected']
                ];
            }, $activities);
            
            echo json_encode($formattedActivities);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la récupération de l\'activité']);
        }
    }

    // API pour la gestion des utilisateurs
    public function apiUsers() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        
        try {
            $db = \App\Helpers\Database::getConnection();
            
            $page = $_GET['page'] ?? 1;
            $search = $_GET['search'] ?? '';
            $rank = $_GET['rank'] ?? '';
            $status = $_GET['status'] ?? '';
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            // Construire la requête avec filtres
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.pseudo LIKE ? OR u.email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if (!empty($rank)) {
                $whereConditions[] = "r.name = ?";
                $params[] = $rank;
            }
            
            if (!empty($status)) {
                $whereConditions[] = "u.desactivated = ?";
                $params[] = $status === 'banned' ? 1 : 0;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Compter le total d'utilisateurs
            $countQuery = "
                SELECT COUNT(*) as total 
                FROM users u 
                LEFT JOIN ranks r ON u.user_rank = r.name 
                $whereClause
            ";
            
            $stmt = $db->prepare($countQuery);
            $stmt->execute($params);
            $total = $stmt->fetch()['total'];
            
            // Récupérer les utilisateurs avec pagination
            $query = "
                SELECT u.id, u.pseudo, u.email, u.desactivated as status, u.connected, u.email_verified, u.point,
                       r.id as rank_id, r.name as rank_name, r.display_name as rank_display_name, 
                       r.color as rank_color, r.background_color as rank_bg_color,
                       COUNT(up.id) as permissions_count
                FROM users u 
                LEFT JOIN ranks r ON u.user_rank = r.name 
                LEFT JOIN user_permissions up ON u.id = up.user_id
                $whereClause
                GROUP BY u.id, u.pseudo, u.email, u.desactivated, u.connected, u.email_verified, u.point,
                         r.id, r.name, r.display_name, r.color, r.background_color
                ORDER BY r.priority DESC, u.pseudo ASC 
                LIMIT $limit OFFSET $offset
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $users = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'users' => $users,
                'total_pages' => ceil($total / $limit),
                'current_page' => $page,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la récupération des utilisateurs', 'details' => $e->getMessage()]);
        }
    }

    // API pour récupérer/modifier un utilisateur spécifique
    public function apiUser($userId) {
        // Capturer toutes les erreurs et les rediriger vers JSON
        set_error_handler(function($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            
            // Log l'erreur
            $errorMessage = date('[d/m/Y H:i:s] ') . "Erreur PHP: $message dans $file ligne $line\n";
            file_put_contents(__DIR__ . '/../storage/logs/php_errors.log', $errorMessage, FILE_APPEND);
            
            // Retourner une réponse JSON d'erreur
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur serveur interne', 'details' => $message]);
            exit;
        });
        
        // S'assurer que la sortie est en JSON
        header('Content-Type: application/json');
        
        // Désactiver l'affichage des erreurs pour cette requête
        ini_set('display_errors', 0);
        
        try {
            if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            $db = \App\Helpers\Database::getConnection();
            
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    // Récupérer les informations de l'utilisateur
                    $stmt = $db->prepare("
                        SELECT u.id, u.pseudo, u.email, u.desactivated as status, u.connected, u.email_verified,
                               r.id as rank_id, r.name as rank_name, r.display_name as rank_display_name,
                               r.color as rank_color, r.background_color as rank_bg_color
                        FROM users u 
                        LEFT JOIN ranks r ON u.user_rank = r.name 
                        WHERE u.id = ?
                    ");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    if (!$user) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Utilisateur non trouvé']);
                        exit;
                    }
                    
                    // Convertir le statut numérique en texte
                    switch ($user['status']) {
                        case 0:
                            $user['status_text'] = 'active';
                            break;
                        case 1:
                            $user['status_text'] = 'banned';
                            break;
                        case 2:
                            $user['status_text'] = 'inactive';
                            break;
                        default:
                            $user['status_text'] = 'active';
                            break;
                    }
                    
                    // Récupérer les permissions de l'utilisateur
                    $stmt = $db->prepare("
                        SELECT p.id, p.name, p.description
                        FROM user_permissions up
                        JOIN permissions p ON up.permission_id = p.id
                        WHERE up.user_id = ?
                    ");
                    $stmt->execute([$userId]);
                    $user['permissions'] = $stmt->fetchAll();
                    
                    echo json_encode($user);
                    break;
                    
                case 'PUT':
                    // Empêcher la modification de son propre compte
                    if ($userId == $_SESSION['user_id']) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Vous ne pouvez pas modifier votre propre compte']);
                        exit;
                    }
                    
                    // Modifier l'utilisateur
                    $input = file_get_contents('php://input');
                    $data = json_decode($input, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Données JSON invalides']);
                        exit;
                    }
                    
                    $pseudo = $data['username'] ?? '';
                    $email = $data['email'] ?? '';
                    $rankName = $data['rank_name'] ?? null;
                    $status = $data['status'] ?? 'active';
                    $permissions = $data['permissions'] ?? [];
                    
                    if (empty($pseudo) || empty($email)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Nom d\'utilisateur et email requis']);
                        exit;
                    }
                    
                    // Validation de l'email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email invalide']);
                        exit;
                    }
                    
                    // Vérifier si l'email existe déjà pour un autre utilisateur
                    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $userId]);
                    if ($stmt->fetch()) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Cet email est déjà utilisé par un autre utilisateur']);
                        exit;
                    }
                    
                    // Vérifier si le pseudo existe déjà pour un autre utilisateur
                    $stmt = $db->prepare("SELECT id FROM users WHERE pseudo = ? AND id != ?");
                    $stmt->execute([$pseudo, $userId]);
                    if ($stmt->fetch()) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Ce nom d\'utilisateur est déjà utilisé par un autre utilisateur']);
                        exit;
                    }
                    
                    // Convertir le statut texte en numérique
                    $statusNum = 0; // Actif par défaut
                    switch ($status) {
                        case 'banned':
                            $statusNum = 1;
                            break;
                        case 'inactive':
                            $statusNum = 2;
                            break;
                        case 'active':
                        default:
                            $statusNum = 0;
                            break;
                    }
                    
                    // Mettre à jour l'utilisateur
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET pseudo = ?, email = ?, user_rank = ?, desactivated = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$pseudo, $email, $rankName, $statusNum, $userId]);
                    
                    // Mettre à jour les permissions
                    $stmt = $db->prepare("DELETE FROM user_permissions WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    
                    if (!empty($permissions)) {
                        $stmt = $db->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (?, ?)");
                        foreach ($permissions as $permissionId) {
                            $stmt->execute([$userId, $permissionId]);
                        }
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
                    break;
                    
                case 'PATCH':
                    // Mise à jour partielle (pour les points)
                    $input = file_get_contents('php://input');
                    $data = json_decode($input, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Données JSON invalides']);
                        exit;
                    }
                    
                    // Si c'est une mise à jour des points
                    if (isset($data['points'])) {
                        $newPoints = intval($data['points']);
                        $reason = trim($data['reason'] ?? '');
                        
                        if ($newPoints < 0) {
                            http_response_code(400);
                            echo json_encode(['error' => 'Les points ne peuvent pas être négatifs']);
                            exit;
                        }
                        
                        // Récupérer les points actuels
                        $stmt = $db->prepare("SELECT point FROM users WHERE id = ?");
                        $stmt->execute([$userId]);
                        $currentPoints = $stmt->fetchColumn();
                        
                        // Mettre à jour les points
                        $stmt = $db->prepare("UPDATE users SET point = ? WHERE id = ?");
                        $stmt->execute([$newPoints, $userId]);
                        
                        // Logger le changement
                        $stmt = $db->prepare("
                            INSERT INTO point_history (user_id, old_points, new_points, reason, admin_id, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$userId, $currentPoints, $newPoints, $reason, $_SESSION['user_id']]);
                        
                        echo json_encode(['success' => true, 'message' => 'Points mis à jour avec succès']);
                        break;
                    }
                    
                    http_response_code(400);
                    echo json_encode(['error' => 'Action non reconnue']);
                    break;
                    
                case 'DELETE':
                    // Supprimer l'utilisateur
                    if ($userId == $_SESSION['user_id']) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Vous ne pouvez pas supprimer votre propre compte']);
                        exit;
                    }
                    
                    $db->beginTransaction();
                    
                    try {
                        // Supprimer les permissions de l'utilisateur
                        $stmt = $db->prepare("DELETE FROM user_permissions WHERE user_id = ?");
                        $stmt->execute([$userId]);
                        
                        // Supprimer l'utilisateur
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$userId]);
                        
                        if ($stmt->rowCount() == 0) {
                            throw new \Exception('Utilisateur non trouvé');
                        }
                        
                        $db->commit();
                        echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
                    } catch (\Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                    break;
                    
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Méthode non autorisée']);
                    break;
            }
        } catch (\Exception $e) {
            // Log manuel de l'erreur
            $logMessage = date('[d/m/Y H:i:s] ') . 'Erreur modification utilisateur : ' . $e->getMessage() . "\n";
            file_put_contents(__DIR__ . '/../storage/logs/log_28-06-2025.txt', $logMessage, FILE_APPEND);
            
            http_response_code(500);
            echo json_encode([
                'error' => 'Erreur lors de l\'opération sur l\'utilisateur', 
                'details' => $e->getMessage()
            ]);
        } finally {
            // Restaurer le gestionnaire d'erreur par défaut
            restore_error_handler();
        }
    }

    // API pour basculer le statut d'un utilisateur
    public function apiToggleUserStatus($userId) {
        try {
            if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            // Empêcher de modifier son propre compte
            if ($userId == $_SESSION['user_id']) {
                http_response_code(400);
                echo json_encode(['error' => 'Vous ne pouvez pas modifier votre propre statut']);
                exit;
            }
            
            $db = \App\Helpers\Database::getConnection();
            
            // Récupérer le statut actuel
            $stmt = $db->prepare("SELECT desactivated FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'Utilisateur non trouvé']);
                exit;
            }
            
            // Faire circuler les statuts : actif (0) -> banni (1) -> inactif (2) -> actif (0)
            $currentStatus = $user['desactivated'];
            $newStatus = ($currentStatus + 1) % 3;
            
            $stmt = $db->prepare("UPDATE users SET desactivated = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);
            
            // Déterminer le texte du statut
            $statusText = '';
            switch ($newStatus) {
                case 0:
                    $statusText = 'activé';
                    break;
                case 1:
                    $statusText = 'banni';
                    break;
                case 2:
                    $statusText = 'désactivé (inactif)';
                    break;
            }
            
            echo json_encode(['success' => true, 'message' => "Utilisateur $statusText"]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    public function reports()
    {
        // Vérification permission admin
        if (!isset($_SESSION['user_id']) || !in_array('admin.reports', $_SESSION['permissions'] ?? []) && !in_array('*', $_SESSION['permissions'] ?? [])) {
            header('Location: /');
            exit;
        }
        $db = \App\Helpers\Database::getConnection();
        $stmt = $db->query("SELECT r.*, u.pseudo AS reporter FROM reports r JOIN users u ON r.reporter_id = u.id ORDER BY r.created_at DESC LIMIT 100");
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../Views/admin/reports.php';
    }

    public function viewReport()
    {
        // Vérification permission admin
        if (!isset($_SESSION['user_id']) || !in_array('admin.reports', $_SESSION['permissions'] ?? []) && !in_array('*', $_SESSION['permissions'] ?? [])) {
            header('Location: /');
            exit;
        }
        
        $reportId = $_GET['id'] ?? null;
        if (!$reportId) {
            header('Location: /admin/reports');
            exit;
        }
        
        $db = \App\Helpers\Database::getConnection();
        
        // Récupérer le signalement avec les infos utilisateur
        $stmt = $db->prepare("
            SELECT r.*, u.pseudo AS reporter, u.id AS reporter_id
            FROM reports r 
            JOIN users u ON r.reporter_id = u.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$reportId]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            header('Location: /admin/reports');
            exit;
        }
        
        // Récupérer le contenu signalé selon le type
        $content = null;
        if ($report['type'] === 'post') {
            $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$report['target_id']]);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($report['type'] === 'message') {
            $stmt = $db->prepare("SELECT * FROM messages WHERE id = ?");
            $stmt->execute([$report['target_id']]);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Récupérer l'historique des sanctions pour l'utilisateur signalé
        $stmt = $db->prepare("
            SELECT s.*, u.pseudo AS admin_name 
            FROM sanctions s 
            JOIN users u ON s.admin_id = u.id 
            WHERE s.user_id = ? 
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$content['user_id'] ?? 0]);
        $sanctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../Views/admin/report_detail.php';
    }
    
    public function handleReportAction() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.reports')) {
            header('Location: /error/403');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/reports');
            exit;
        }

        $reportId = $_POST['report_id'] ?? null;
        $action = $_POST['action'] ?? '';
        $reason = $_POST['reason'] ?? '';

        if (!$reportId || !$action) {
            $_SESSION['error'] = "Action invalide.";
            header('Location: /admin/reports');
            exit;
        }

        try {
            $db = Database::getConnection();
            
            // Marquer le rapport comme traité
            $stmt = $db->prepare("UPDATE reports SET status = ?, admin_notes = ?, resolved_at = NOW() WHERE id = ?");
            $stmt->execute([$action, $reason, $reportId]);

            $_SESSION['success'] = "Rapport traité avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors du traitement du rapport: " . $e->getMessage();
        }

        header('Location: /admin/reports');
        exit;
    }

    // === GESTION DES RÉCOMPENSES ===
    
    public function rewards() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.rewards')) {
            header('Location: /error/403');
            exit;
        }

        // Récupérer les statistiques
        $db = Database::getConnection();
        
        // Total des récompenses
        $stmt = $db->prepare("SELECT COUNT(*) FROM rewards");
        $stmt->execute();
        $totalRewards = $stmt->fetchColumn();
        
        // Total des déblocages
        $stmt = $db->prepare("SELECT COUNT(*) FROM user_rewards");
        $stmt->execute();
        $totalUnlocks = $stmt->fetchColumn();
        
        // Récompenses physiques
        $stmt = $db->prepare("SELECT COUNT(*) FROM rewards WHERE type = 'physical'");
        $stmt->execute();
        $physicalRewards = $stmt->fetchColumn();
        
        // Total des points distribués
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(r.points_value), 0) 
            FROM rewards r 
            JOIN user_rewards ur ON r.id = ur.reward_id
        ");
        $stmt->execute();
        $totalPoints = $stmt->fetchColumn();
        
        // Récupérer toutes les récompenses avec statistiques
        $stmt = $db->prepare("
            SELECT r.*, 
                   COUNT(ur.user_id) as unlock_count,
                   (SELECT COUNT(*) FROM users) as total_users
            FROM rewards r
            LEFT JOIN user_rewards ur ON r.id = ur.reward_id
            GROUP BY r.id
            ORDER BY r.required_level ASC, r.name ASC
        ");
        $stmt->execute();
        $rewards = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/admin/rewards.php';
    }
    
    public function createReward() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.rewards')) {
            header('Location: /error/403');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/rewards');
            exit;
        }

        try {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'],
                'icon' => trim($_POST['icon']),
                'required_level' => intval($_POST['required_level']),
                'rarity' => $_POST['rarity'],
                'points_value' => intval($_POST['points_value'])
            ];

            // Validation
            if (empty($data['name']) || empty($data['description'])) {
                throw new \Exception("Le nom et la description sont requis.");
            }

            // Ajouter les champs spécifiques aux récompenses physiques
            if ($data['type'] === 'physical') {
                $data['price'] = floatval($_POST['price'] ?? 0);
                $data['stock'] = intval($_POST['stock'] ?? 0);
            }

            \App\Models\Reward::create($data);
            $_SESSION['success'] = "Récompense créée avec succès.";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la création: " . $e->getMessage();
        }

        header('Location: /admin/rewards');
        exit;
    }
    
    public function editReward($id) {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.rewards')) {
            header('Location: /error/403');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/rewards');
            exit;
        }

        try {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'],
                'icon' => trim($_POST['icon']),
                'required_level' => intval($_POST['required_level']),
                'rarity' => $_POST['rarity'],
                'points_value' => intval($_POST['points_value'])
            ];

            // Validation
            if (empty($data['name']) || empty($data['description'])) {
                throw new \Exception("Le nom et la description sont requis.");
            }

            // Ajouter les champs spécifiques aux récompenses physiques
            if ($data['type'] === 'physical') {
                $data['price'] = floatval($_POST['price'] ?? 0);
                $data['stock'] = intval($_POST['stock'] ?? 0);
            }

            \App\Models\Reward::update($id, $data);
            $_SESSION['success'] = "Récompense mise à jour avec succès.";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la mise à jour: " . $e->getMessage();
        }

        header('Location: /admin/rewards');
        exit;
    }
    
    public function deleteReward($id) {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.rewards')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        try {
            \App\Models\Reward::delete($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // === API POUR LES RÉCOMPENSES ===
    
    public function apiReward($id) {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.rewards')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        try {
            $reward = \App\Models\Reward::getById($id);
            if ($reward) {
                echo json_encode(['success' => true, 'reward' => $reward]);
            } else {
                echo json_encode(['error' => 'Récompense introuvable']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiUserPointsHistory($userId) {
        // Capturer toutes les erreurs et les rediriger vers JSON
        set_error_handler(function($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            
            // Log l'erreur
            $errorMessage = date('[d/m/Y H:i:s] ') . "Erreur PHP: $message dans $file ligne $line\n";
            file_put_contents(__DIR__ . '/../storage/logs/php_errors.log', $errorMessage, FILE_APPEND);
            
            // Retourner une réponse JSON d'erreur
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur serveur interne', 'details' => $message]);
            exit;
        });
        
        // S'assurer que la sortie est en JSON
        header('Content-Type: application/json');
        
        // Désactiver l'affichage des erreurs pour cette requête
        ini_set('display_errors', 0);
        
        try {
            if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            $db = \App\Helpers\Database::getConnection();
            
            // Récupérer l'historique des points
            $stmt = $db->prepare("
                SELECT ph.*, u.pseudo as admin_name
                FROM point_history ph
                LEFT JOIN users u ON ph.admin_id = u.id
                WHERE ph.user_id = ?
                ORDER BY ph.created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$userId]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['history' => $history]);
        } catch (\Exception $e) {
            // Log manuel de l'erreur
            $logMessage = date('[d/m/Y H:i:s] ') . 'Erreur récupération historique points : ' . $e->getMessage() . "\n";
            file_put_contents(__DIR__ . '/../storage/logs/log_28-06-2025.txt', $logMessage, FILE_APPEND);
            
            http_response_code(500);
            echo json_encode([
                'error' => 'Erreur lors de la récupération de l\'historique des points', 
                'details' => $e->getMessage()
            ]);
        } finally {
            // Restaurer le gestionnaire d'erreur par défaut
            restore_error_handler();
        }
    }
} 