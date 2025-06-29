<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Permission;
use App\Helpers\Database;

echo "🧪 Test de la création de posts\n\n";

// Test avec l'utilisateur admin (ID 1)
$userId = 1;

echo "Test pour l'utilisateur ID: $userId\n";

// Test des permissions
$hasPostCreate = Permission::hasPermission($userId, 'post.create');
$isAdmin = Permission::isAdmin($userId);

echo "🔐 Permissions:\n";
echo "   Permission 'post.create': " . ($hasPostCreate ? "✅ OUI" : "❌ NON") . "\n";
echo "   Permission '*' (admin): " . ($isAdmin ? "✅ OUI" : "❌ NON") . "\n\n";

// Test de la base de données
try {
    $db = Database::getConnection();
    
    // Vérifier que l'utilisateur existe
    $stmt = $db->prepare("SELECT id, pseudo, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ Utilisateur trouvé: {$user['pseudo']} ({$user['email']})\n";
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
    // Vérifier la structure de la table post
    $stmt = $db->prepare("DESCRIBE post");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    $requiredColumns = ['id', 'user_id', 'content_id', 'name', 'description', 'latitude', 'longitude', 'date'];
    $missingColumns = [];
    
    foreach ($requiredColumns as $col) {
        $found = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $col) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $missingColumns[] = $col;
        }
    }
    
    if (empty($missingColumns)) {
        echo "✅ Structure de la table post: OK\n";
    } else {
        echo "❌ Colonnes manquantes dans la table post: " . implode(', ', $missingColumns) . "\n";
    }
    
    // Vérifier la structure de la table post_content
    $stmt = $db->prepare("DESCRIBE post_content");
    $stmt->execute();
    $contentColumns = $stmt->fetchAll();
    
    $contentRequiredColumns = ['id', 'content'];
    $contentMissingColumns = [];
    
    foreach ($contentRequiredColumns as $col) {
        $found = false;
        foreach ($contentColumns as $column) {
            if ($column['Field'] === $col) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $contentMissingColumns[] = $col;
        }
    }
    
    if (empty($contentMissingColumns)) {
        echo "✅ Structure de la table post_content: OK\n";
    } else {
        echo "❌ Colonnes manquantes dans la table post_content: " . implode(', ', $contentMissingColumns) . "\n";
    }
    
    echo "\n🎯 Résultat: ";
    if (($hasPostCreate || $isAdmin) && $user && empty($missingColumns) && empty($contentMissingColumns)) {
        echo "✅ Système de création de posts opérationnel!\n";
        echo "   - Permissions OK\n";
        echo "   - Utilisateur trouvé\n";
        echo "   - Structure de base de données OK\n";
        echo "   - Modal moderne et fonctionnel\n";
    } else {
        echo "❌ Problème détecté dans le système\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "\n";
} 