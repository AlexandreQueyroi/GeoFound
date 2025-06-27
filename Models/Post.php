<?php
namespace App\Models;
use App\Helpers\Database;

class Post {
    private static $db;
    public static function init() {
        if (!self::$db) {
            self::$db = Database::getConnection();
        }
    }

    public static function all() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM post');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function create($data) {
        try {
            $pdo = Database::getConnection();

            
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                \App\Helpers\Logger::error('Aucune image valide reçue', 'Post::create');
                return false;
            }
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_data = file_get_contents($image_tmp);
            $image_base64 = base64_encode($image_data);

            
            $sqlContent = "INSERT INTO post_content (content) VALUES (?)";
            $stmtContent = $pdo->prepare($sqlContent);
            $stmtContent->execute([$image_base64]);
            $content_id = $pdo->lastInsertId();

            
            $sql = "INSERT INTO post (user_id, content_id, name, description, latitude, longitude, date) 
                    VALUES (:user_id, :content_id, :name, :description, :latitude, :longitude, :date)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'user_id' => $data['user_id'],
                'content_id' => $content_id,
                'name' => $data['name'],
                'description' => $data['description'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'date' => $data['date']
            ]);
            return $pdo->lastInsertId();
        } catch (\Exception $e) {
            \App\Helpers\Logger::error('Erreur création post: ' . $e->getMessage(), 'Post::create');
            return false;
        }
    }
    
    public static function findById($id) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT * FROM post WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Erreur recherche post: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function findByUser($userId) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT * FROM post WHERE user = ? ORDER BY date DESC');
            $stmt->execute([$userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Erreur recherche posts utilisateur: ' . $e->getMessage());
            return [];
        }
    }

    public static function findPaginated($offset = 0, $limit = 6) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT p.*, u.pseudo as username, pc.content,
                (SELECT COUNT(*) FROM reaction r WHERE r.post_id = p.id AND r.state = 'like') as like_count,
                (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id) as comment_count
            FROM post p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN post_content pc ON p.content_id = pc.id
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 