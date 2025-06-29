<?php
require_once __DIR__ . '/vendor/autoload.php';

// Démarrer la session
session_start();

echo "=== Test de la messagerie ===\n";

// Test 1: Vérifier la connexion à la base de données
echo "\n1. Test de connexion à la base de données...\n";
try {
    $pdo = \App\Helpers\Database::getConnection();
    echo "✓ Connexion à la base de données réussie\n";
} catch (Exception $e) {
    echo "✗ Erreur de connexion: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Vérifier les tables de messagerie
echo "\n2. Vérification des tables de messagerie...\n";
$tables = ['message', 'user_message', 'users', 'follow'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✓ Table '$table' existe avec " . count($columns) . " colonnes\n";
    } catch (Exception $e) {
        echo "✗ Table '$table' manquante ou erreur: " . $e->getMessage() . "\n";
    }
}

// Test 3: Vérifier les utilisateurs
echo "\n3. Vérification des utilisateurs...\n";
try {
    $stmt = $pdo->query("SELECT id, pseudo, email FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ " . count($users) . " utilisateurs trouvés:\n";
    foreach ($users as $user) {
        echo "  - ID: {$user['id']}, Pseudo: {$user['pseudo']}, Email: {$user['email']}\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors de la récupération des utilisateurs: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier les relations d'amis
echo "\n4. Vérification des relations d'amis...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM follow WHERE state = 'accepted'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ " . $result['count'] . " relations d'amis acceptées\n";
} catch (Exception $e) {
    echo "✗ Erreur lors de la vérification des amis: " . $e->getMessage() . "\n";
}

// Test 5: Vérifier les messages existants
echo "\n5. Vérification des messages...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM message");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ " . $result['count'] . " messages dans la base\n";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query("SELECT m.*, um.sender_id, um.receiver_id FROM message m JOIN user_message um ON m.id = um.message_id LIMIT 3");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "  Exemples de messages:\n";
        foreach ($messages as $msg) {
            echo "  - ID: {$msg['id']}, Contenu: " . substr($msg['content'], 0, 50) . "..., Expéditeur: {$msg['sender_id']}, Destinataire: {$msg['receiver_id']}\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Erreur lors de la vérification des messages: " . $e->getMessage() . "\n";
}

// Test 6: Test du modèle Message
echo "\n6. Test du modèle Message...\n";
try {
    $messageModel = new \App\Models\Message();
    echo "✓ Modèle Message instancié avec succès\n";
} catch (Exception $e) {
    echo "✗ Erreur lors de l'instanciation du modèle Message: " . $e->getMessage() . "\n";
}

// Test 7: Test du modèle Friend
echo "\n7. Test du modèle Friend...\n";
try {
    // Simuler un utilisateur connecté
    $_SESSION['user_id'] = 1;
    
    $friends = \App\Models\Friend::getFriends(1);
    echo "✓ " . count($friends) . " amis trouvés pour l'utilisateur 1\n";
    
    if (!empty($friends)) {
        echo "  Premier ami: " . $friends[0]['pseudo'] . "\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur lors de la récupération des amis: " . $e->getMessage() . "\n";
}

// Test 8: Test du contrôleur MessageController
echo "\n8. Test du contrôleur MessageController...\n";
try {
    $messageController = new \App\Controllers\MessageController();
    echo "✓ MessageController instancié avec succès\n";
} catch (Exception $e) {
    echo "✗ Erreur lors de l'instanciation du MessageController: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test ===\n"; 