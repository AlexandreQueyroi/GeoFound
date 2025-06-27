<?php
namespace App\Controllers;
use App\Helpers\Database;
use App\Helpers\Logger;
use Exception;

class ApiController {
    public function user($id = null) {
        $pdo = Database::getConnection();
        session_start();
        
        if (isset($_GET["checkuser"])) {
            $user = htmlspecialchars($_GET["checkuser"]);
            $stmt = $pdo->prepare("SELECT id FROM users WHERE pseudo = :pseudo");
            $stmt->bindParam(':pseudo', $user, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "exist";
            }
            exit;
        }
        
        if (isset($_GET["checkemail"])) {
            $user = htmlspecialchars($_GET["checkemail"]);
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $user, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "exist";
            }
            exit;
        }
        
        if ($id) {
            header('Content-Type: application/json');
            echo json_encode(['id' => $id, 'name' => 'Utilisateur exemple']);
        }
    }
    
    public function captcha() {
        $pdo = Database::getConnection();
        header('Content-Type: application/json');
        
        $query = $pdo->prepare("SELECT title, response FROM captcha WHERE enabled = 1 ORDER BY RAND() LIMIT 1");
        $query->execute();
        $captcha = $query->fetch(\PDO::FETCH_ASSOC);

        if ($captcha) {
            session_start();
            $response = [
                'question' => $captcha['title'],
                'answer' => $captcha['response']
            ];
            
            echo json_encode($response);
        } else {
            die("Aucun captcha n'a été trouvé.");
        }
    }

    public function friends() {
        session_start();
        if (!isset($_SESSION['user_id'])) { echo 'no session'; exit; }
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $sql = 'SELECT u.id, u.pseudo,
                (SELECT COUNT(*) FROM message m
                 JOIN user_message um ON m.id = um.message_id
                 WHERE um.sender_id = u.id
                 AND um.receiver_id = :uid
                 AND m.state = "sent") as unread_count
                FROM users u
                JOIN follow f ON ((f.user1_id = :uid AND f.user2_id = u.id)
                OR (f.user2_id = :uid AND f.user1_id = u.id))
                WHERE f.state = "accepted"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $uid]);
        $friends = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($friends);
    }

    public function messages() {
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $fid = intval($_GET['friend_id'] ?? 0);
        if ($fid == 0) exit('[]');
        $stmt = $pdo->prepare('SELECT * FROM follow WHERE ((user1_id=:uid AND user2_id=:fid) OR (user2_id=:uid AND user1_id=:fid)) AND state="accepted"');
        $stmt->execute(['uid'=>$uid,'fid'=>$fid]);
        if ($stmt->rowCount() == 0) exit('[]');
        $sql = 'SELECT m.id, m.content, m.posted_at, um.sender_id, m.state 
                FROM message m 
                JOIN user_message um ON m.id = um.message_id 
                WHERE (um.sender_id = :uid AND um.receiver_id = :fid) 
                   OR (um.sender_id = :fid AND um.receiver_id = :uid) 
                ORDER BY m.posted_at ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid'=>$uid, 'fid'=>$fid]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        foreach ($messages as $m) {
            $result[] = [
                'content' => $m['content'],
                'sent' => ($m['sender_id'] == $uid),
                'time' => $m['posted_at'],
                'state' => $m['state']
            ];
        }
        $update = $pdo->prepare('UPDATE message m
            JOIN user_message um ON m.id = um.message_id
            SET m.state = "read"
            WHERE um.receiver_id = :uid AND um.sender_id = :fid AND m.state = "sent"');
        $update->execute(['uid' => $uid, 'fid' => $fid]);
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function send_message() {
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $to = intval($_POST['to'] ?? 0);
        $content = trim($_POST['message'] ?? '');
        if ($to == 0 || $content == '') exit;
        $stmt = $pdo->prepare('SELECT * FROM follow WHERE ((user1_id=:uid AND user2_id=:to) OR (user2_id=:uid AND user1_id=:to)) AND state="accepted"');
        $stmt->execute(['uid'=>$uid,'to'=>$to]);
        if ($stmt->rowCount() == 0) exit;
        $stmt = $pdo->prepare('INSERT INTO message (posted_at, content, state) VALUES (NOW(), :content, "sent")');
        $stmt->execute(['content'=>$content]);
        $message_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO user_message (message_id, sender_id, receiver_id) VALUES (:message_id, :sender_id, :receiver_id)');
        $stmt->execute([
            'message_id' => $message_id,
            'sender_id' => $uid,
            'receiver_id' => $to
        ]);
        echo 'ok';
    }

    public function friend_requests() {
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $sql = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user1_id=u.id WHERE f.user2_id=:uid AND f.state='pending'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid'=>$uid]);
        $received = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $sql2 = "SELECT f.id, u.pseudo FROM follow f JOIN users u ON f.user2_id=u.id WHERE f.user1_id=:uid AND f.state='pending'";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute(['uid'=>$uid]);
        $sent = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['received'=>$received, 'sent'=>$sent]);
    }

    public function add_friend() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $pseudo = trim($_POST['pseudo'] ?? '');
        if ($pseudo == '') exit('Pseudo requis');
        $stmt = $pdo->prepare('SELECT id FROM users WHERE pseudo = :pseudo');
        $stmt->execute(['pseudo'=>$pseudo]);
        $user = $stmt->fetch();
        if (!$user) exit('Utilisateur introuvable');
        $fid = $user['id'];
        if ($fid == $uid) exit('Impossible de s\'ajouter soi-même');
        $stmt = $pdo->prepare('SELECT * FROM follow WHERE (user1_id=:uid AND user2_id=:fid) OR (user1_id=:fid AND user2_id=:uid)');
        $stmt->execute(['uid'=>$uid,'fid'=>$fid]);
        if ($stmt->rowCount() > 0) exit('Demande déjà envoyée');
        $stmt = $pdo->prepare('INSERT INTO follow (user1_id, user2_id, follow_at, state) VALUES (:uid, :fid, NOW(), "pending")');
        $stmt->execute(['uid'=>$uid,'fid'=>$fid]);
        echo 'Demande envoyée';
    }

    public function accept_friend() {
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE follow SET state="accepted" WHERE id=:id AND user2_id=:uid');
        $stmt->execute(['id'=>$id,'uid'=>$uid]);
        echo 'Ami accepté';
    }

    public function refuse_friend() {
        session_start();
        if (!isset($_SESSION['user_id'])) exit;
        $pdo = Database::getConnection();
        $uid = $_SESSION['user_id'];
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM follow WHERE id=:id AND user2_id=:uid');
        $stmt->execute(['id'=>$id,'uid'=>$uid]);
        echo 'Demande refusée';
    }

    public function react() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $post_id = intval($_POST['post_id'] ?? 0);
        $user_id = $_SESSION['user_id'];
        $state = $_POST['state'] ?? 'like';

        if ($post_id === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de post invalide']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT id, state FROM reaction WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$post_id, $user_id]);
            $existing_reaction = $stmt->fetch();

            if ($existing_reaction) {
                if ($existing_reaction['state'] === $state) {
                    
                    $stmt = $pdo->prepare("DELETE FROM reaction WHERE id = ?");
                    $stmt->execute([$existing_reaction['id']]);
                    echo json_encode(['action' => 'removed', 'state' => $state]);
                } else {
                    
                    $stmt = $pdo->prepare("UPDATE reaction SET state = ?, react_at = NOW() WHERE id = ?");
                    $stmt->execute([$state, $existing_reaction['id']]);
                    echo json_encode(['action' => 'updated', 'state' => $state]);
                }
            } else {
                
                $stmt = $pdo->prepare("INSERT INTO reaction (post_id, user_id, state) VALUES (?, ?, ?)");
                $stmt->execute([$post_id, $user_id, $state]);
                echo json_encode(['action' => 'added', 'state' => $state]);
            }
        } catch (Exception $e) {
            Logger::error('Erreur lors de la réaction: ' . $e->getMessage(), 'ApiController::react');
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function comment() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $post_id = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $user_id = $_SESSION['user_id'];

        if ($post_id === 0 || empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("INSERT INTO comment (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $content]);
            
            echo json_encode(['success' => true, 'comment_id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            Logger::error('Erreur lors de l\'ajout du commentaire: ' . $e->getMessage(), 'ApiController::comment');
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function comments() {
        $post_id = intval($_GET['post_id'] ?? 0);

        if ($post_id === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de post invalide']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT c.*, u.pseudo as username 
                FROM comment c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = ? 
                ORDER BY c.comment_at DESC
            ");
            $stmt->execute([$post_id]);
            $comments = $stmt->fetchAll();
            
            echo json_encode(['comments' => $comments]);
        } catch (Exception $e) {
            Logger::error('Erreur lors du chargement des commentaires: ' . $e->getMessage(), 'ApiController::comments');
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function bookmark() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $post_id = intval($_POST['post_id'] ?? 0);
        $user_id = $_SESSION['user_id'];

        if ($post_id === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de post invalide']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            
            
            $stmt = $pdo->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ?");
            $stmt->execute([$user_id, $post_id]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                
                $deleteStmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = ?");
                $deleteStmt->execute([$existing['id']]);
                echo json_encode(['success' => true, 'action' => 'removed']);
            } else {
                
                $insertStmt = $pdo->prepare("INSERT INTO bookmarks (user_id, post_id) VALUES (?, ?)");
                $insertStmt->execute([$user_id, $post_id]);
                echo json_encode(['success' => true, 'action' => 'added']);
            }
        } catch (\Exception $e) {
            Logger::error('Erreur lors de la gestion du favori: ' . $e->getMessage(), 'ApiController::bookmark');
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
        }
    }

    public function lastMessage() {
        $contactId = $_GET['contact_id'] ?? null;
        if (!$contactId || !isset($_SESSION['user_id'])) {
            
        }
    }

    public function posts() {
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 6;
        $posts = \App\Models\Post::findPaginated($offset, $limit);
        header('Content-Type: application/json');
        echo json_encode(['posts' => $posts]);
        exit;
    }
} 