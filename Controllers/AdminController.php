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

    public function users() {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            header('Location: /error/403');
            exit;
        }
        require __DIR__ . '/../Views/admin/users.php';
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
            
            // Statistiques des posts
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM posts");
            $stmt->execute();
            $posts = $stmt->fetch()['count'];
            
            // Statistiques des messages
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM messages");
            $stmt->execute();
            $messages = $stmt->fetch()['count'];
            
            // Pages en maintenance
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM page_maintenance WHERE is_maintenance = 1");
            $stmt->execute();
            $maintenance = $stmt->fetch()['count'];
            
            echo json_encode([
                'users' => $users,
                'posts' => $posts,
                'messages' => $messages,
                'maintenance' => $maintenance
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la récupération des statistiques']);
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
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
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
                $whereConditions[] = "u.rank = ?";
                $params[] = $rank;
            }
            
            if (!empty($status)) {
                $whereConditions[] = "u.desactivated = ?";
                $params[] = $status === 'banned' ? 1 : 0;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Requête pour compter le total
            $countQuery = "
                SELECT COUNT(*) as total 
                FROM users u 
                LEFT JOIN ranks r ON u.rank = r.id 
                $whereClause
            ";
            $stmt = $db->prepare($countQuery);
            $stmt->execute($params);
            $total = $stmt->fetch()['total'];
            
            // Requête pour récupérer les utilisateurs
            $query = "
                SELECT u.id, u.pseudo, u.email, u.desactivated as status, u.connected,
                       r.id as rank_id, r.name as rank_name, r.color as rank_color,
                       COUNT(up.id) as permissions_count
                FROM users u 
                LEFT JOIN ranks r ON u.rank = r.id 
                LEFT JOIN user_permissions up ON u.id = up.user_id
                $whereClause
                GROUP BY u.id, u.pseudo, u.email, u.desactivated, u.connected, r.id, r.name, r.color
                ORDER BY u.id DESC 
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
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        try {
            $db = \App\Helpers\Database::getConnection();
            
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    // Récupérer les informations de l'utilisateur
                    $stmt = $db->prepare("
                        SELECT u.id, u.pseudo, u.email, u.desactivated as status, u.connected,
                               r.id as rank_id, r.name as rank_name, r.color as rank_color
                        FROM users u 
                        LEFT JOIN ranks r ON u.rank = r.id 
                        WHERE u.id = ?
                    ");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    if (!$user) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Utilisateur non trouvé']);
                        exit;
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
                    // Modifier l'utilisateur
                    $data = json_decode(file_get_contents('php://input'), true);
                    
                    $pseudo = $data['username'] ?? '';
                    $email = $data['email'] ?? '';
                    $rankId = $data['rank_id'] ?? null;
                    $status = $data['status'] ?? 'active';
                    $permissions = $data['permissions'] ?? [];
                    
                    if (empty($pseudo) || empty($email)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Nom d\'utilisateur et email requis']);
                        exit;
                    }
                    
                    $db->beginTransaction();
                    
                    try {
                        // Mettre à jour l'utilisateur
                        $stmt = $db->prepare("
                            UPDATE users 
                            SET pseudo = ?, email = ?, rank = ?, desactivated = ?
                            WHERE id = ?
                        ");
                        $desactivated = $status === 'banned' ? 1 : 0;
                        $stmt->execute([$pseudo, $email, $rankId, $desactivated, $userId]);
                        
                        // Supprimer les anciennes permissions
                        $stmt = $db->prepare("DELETE FROM user_permissions WHERE user_id = ?");
                        $stmt->execute([$userId]);
                        
                        // Ajouter les nouvelles permissions
                        if (!empty($permissions)) {
                            $stmt = $db->prepare("
                                INSERT INTO user_permissions (user_id, permission_id, created_by) 
                                VALUES (?, ?, ?)
                            ");
                            foreach ($permissions as $permissionId) {
                                $stmt->execute([$userId, $permissionId, $_SESSION['user_id']]);
                            }
                        }
                        
                        $db->commit();
                        echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour']);
                    } catch (\Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                    break;
                    
                case 'DELETE':
                    // Supprimer l'utilisateur
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    
                    echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
                    break;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de l\'opération sur l\'utilisateur']);
        }
    }

    // API pour basculer le statut d'un utilisateur
    public function apiToggleUserStatus($userId) {
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.users')) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        header('Content-Type: application/json');
        
        try {
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
            
            // Basculer le statut
            $newStatus = $user['desactivated'] ? 0 : 1;
            
            $stmt = $db->prepare("UPDATE users SET desactivated = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Statut modifié',
                'new_status' => $newStatus ? 'banned' : 'active'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la modification du statut']);
        }
    }
} 