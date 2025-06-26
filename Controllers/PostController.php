<?php
namespace App\Controllers;

use App\Models\Permission;
use App\Models\Post;
use App\Helpers\Logger;

class PostController {
    public function index() {
        require __DIR__ . '/../Views/post/index.php';
    }
    
    public function view($id) {
        try {
            $db = \App\Helpers\Database::getConnection();
            $stmt = $db->prepare("SELECT p.*, pc.content, u.pseudo as username FROM post p LEFT JOIN post_content pc ON p.content_id = pc.id LEFT JOIN users u ON p.user_id = u.id WHERE p.id = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch();
            
            if (!$post) {
                header('Location: /error/404');
                exit;
            }
            
            
            $userReaction = null;
            $reactionCounts = [];
            if (isset($_SESSION['user_id'])) {
                $reactionStmt = $db->prepare("SELECT state FROM reaction WHERE post_id = ? AND user_id = ?");
                $reactionStmt->execute([$id, $_SESSION['user_id']]);
                $userReaction = $reactionStmt->fetch();
                
                $countStmt = $db->prepare("SELECT state, COUNT(*) as count FROM reaction WHERE post_id = ? GROUP BY state");
                $countStmt->execute([$id]);
                $reactions = $countStmt->fetchAll();
                foreach ($reactions as $reaction) {
                    $reactionCounts[$reaction['state']] = $reaction['count'];
                }
            }
            
            require __DIR__ . '/../Views/post/view.php';
        } catch (Exception $e) {
            Logger::error('Erreur lors de l\'affichage du post: ' . $e->getMessage(), 'PostController::view');
            header('Location: /error/500');
            exit;
        }
    }
    
    public function create() {
        
        if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'post.create')) {
            Logger::error('Permission refusée - user_id: ' . ($_SESSION['user_id'] ?? 'non défini') . ', permission: post.create', 'PostController::create');
            if ($this->isAjaxRequest()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Permission refusée']);
                return;
            }
            header('Location: /error/403');
            exit;
        }
        
        
        if ($this->isAjaxRequest() && $_SERVER['REQUEST_METHOD'] === 'POST') {
            Logger::info('Création de post demandée', 'PostController::create');
            $this->handleCreatePost();
            return;
        }
        
        
        header('Location: /');
        exit;
    }
    
    private function handleCreatePost() {
        try {
            Logger::info('Début de handleCreatePost', 'PostController::handleCreatePost');
            
            
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $latitude = floatval($_POST['latitude'] ?? 0);
            $longitude = floatval($_POST['longitude'] ?? 0);
            
            Logger::info('Données reçues - title: ' . $title . ', content: ' . $content . ', lat: ' . $latitude . ', lng: ' . $longitude, 'PostController::handleCreatePost');
            
            
            $errors = [];
            if (empty($title)) {
                $errors[] = 'Le titre est requis';
            }
            if (empty($content)) {
                $errors[] = 'La description est requise';
            }
            if ($latitude == 0 && $longitude == 0) {
                $errors[] = 'Les coordonnées GPS sont requises';
            }
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "L'image est obligatoire";
            }
            
            if (!empty($errors)) {
                Logger::error('Erreurs de validation: ' . implode(', ', $errors), 'PostController::handleCreatePost');
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreurs de validation: ' . implode(', ', $errors)
                ]);
                return;
            }
            
            
            
            
            
            
            
            
            $postData = [
                'user_id' => $_SESSION['user_id'],
                'name' => $title,
                'description' => $content,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'date' => date('Y-m-d H:i:s'),
                
                
            ];
            
            Logger::info('Tentative de création du post avec les données: ' . json_encode($postData), 'PostController::handleCreatePost');
            
            $postId = Post::create($postData);
            
            if ($postId) {
                Logger::info('Post créé avec succès, ID: ' . $postId, 'PostController::handleCreatePost');
                echo json_encode([
                    'success' => true,
                    'message' => 'Post créé avec succès',
                    'post_id' => $postId
                ]);
            } else {
                Logger::error('Échec de la création du post', 'PostController::handleCreatePost');
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création du post'
                ]);
            }
            
        } catch (\Exception $e) {
            Logger::error('Exception lors de la création du post: ' . $e->getMessage(), 'PostController::handleCreatePost');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur interne du serveur'
            ]);
        }
    }
    
    private function handleImageUpload($file) {
        $uploadDir = __DIR__ . '/../public/assets/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé');
        }
        
        $maxSize = 5 * 1024 * 1024; 
        if ($file['size'] > $maxSize) {
            throw new Exception('Fichier trop volumineux (max 5MB)');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Erreur lors du téléchargement du fichier');
        }
        
        return '/assets/uploads/posts/' . $filename;
    }
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
} 