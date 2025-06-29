<?php
session_start();

// Inclure les fichiers nécessaires dans le bon ordre
require_once 'vendor/autoload.php';
require_once 'Helpers/Database.php';
require_once 'Models/Permission.php';
require_once 'Models/Rank.php';

// Simuler une session admin
$_SESSION['user_id'] = 1; // ID de l'admin

echo "<h1>Test de la page des grades</h1>";

// Test 1: Vérifier les permissions
echo "<h2>1. Vérification des permissions</h2>";
try {
    $hasPermission = \App\Models\Permission::hasPermission(1, 'admin.rank');
    echo "<p>" . ($hasPermission ? "✅" : "❌") . " Permission admin.rank: " . ($hasPermission ? "OK" : "MANQUANTE") . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 2: Vérifier la table ranks
echo "<h2>2. Vérification de la table ranks</h2>";
try {
    $db = \App\Helpers\Database::getConnection();
    
    $stmt = $db->query("DESCRIBE ranks");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>✅ Table ranks existe avec les colonnes :</p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} - {$column['Type']}</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 3: Vérifier les grades existants
echo "<h2>3. Grades existants</h2>";
try {
    $stmt = $db->query("SELECT * FROM ranks ORDER BY priority DESC");
    $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($ranks)) {
        echo "<p>❌ Aucun grade trouvé</p>";
    } else {
        echo "<p>✅ " . count($ranks) . " grade(s) trouvé(s) :</p>";
        echo "<ul>";
        foreach ($ranks as $rank) {
            echo "<li>{$rank['display_name']} ({$rank['name']}) - Priorité: {$rank['priority']}</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 4: Vérifier les permissions disponibles
echo "<h2>4. Permissions disponibles</h2>";
try {
    $stmt = $db->query("SELECT * FROM permissions ORDER BY name");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($permissions)) {
        echo "<p>❌ Aucune permission trouvée</p>";
    } else {
        echo "<p>✅ " . count($permissions) . " permission(s) trouvée(s) :</p>";
        echo "<ul>";
        foreach ($permissions as $permission) {
            echo "<li>{$permission['name']} - {$permission['description']}</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 5: Vérifier les utilisateurs avec leurs grades
echo "<h2>5. Utilisateurs avec grades</h2>";
try {
    $stmt = $db->prepare("
        SELECT u.id, u.pseudo, u.email, u.user_rank, 
               r.display_name as rank_display_name, r.color as rank_color, r.background_color as rank_bg_color
        FROM users u
        LEFT JOIN ranks r ON u.user_rank = r.name
        ORDER BY r.priority DESC, u.pseudo ASC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>❌ Aucun utilisateur trouvé</p>";
    } else {
        echo "<p>✅ " . count($users) . " utilisateur(s) trouvé(s) :</p>";
        echo "<ul>";
        foreach ($users as $user) {
            $rankInfo = $user['rank_display_name'] ? $user['rank_display_name'] : 'Aucun grade';
            echo "<li>{$user['pseudo']} - {$rankInfo}</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 6: Vérifier les statistiques des grades
echo "<h2>6. Statistiques des grades</h2>";
try {
    $stmt = $db->prepare("
        SELECT r.name, r.display_name, r.color, COUNT(u.id) as user_count
        FROM ranks r
        LEFT JOIN users u ON u.user_rank = r.name
        GROUP BY r.id, r.name, r.display_name, r.color
        ORDER BY r.priority DESC
    ");
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($stats)) {
        echo "<p>❌ Aucune statistique trouvée</p>";
    } else {
        echo "<p>✅ Statistiques :</p>";
        echo "<ul>";
        foreach ($stats as $stat) {
            echo "<li>{$stat['display_name']}: {$stat['user_count']} utilisateur(s)</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 7: Test du modèle Rank
echo "<h2>7. Test du modèle Rank</h2>";
try {
    // Test getAll()
    $allRanks = \App\Models\Rank::getAll();
    echo "<p>✅ Rank::getAll() retourne " . count($allRanks) . " grade(s)</p>";
    
    // Test getStats()
    $rankStats = \App\Models\Rank::getStats();
    echo "<p>✅ Rank::getStats() retourne " . count($rankStats) . " statistique(s)</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>8. Instructions de test</h2>";
echo "<p>Pour tester la page des grades :</p>";
echo "<ol>";
echo "<li>Connectez-vous en tant qu'admin</li>";
echo "<li>Allez sur <a href='/admin/rank'>/admin/rank</a></li>";
echo "<li>Vérifiez que la page se charge correctement</li>";
echo "<li>Testez la création d'un nouveau grade</li>";
echo "<li>Testez la modification d'un grade existant</li>";
echo "<li>Testez l'attribution d'un grade à un utilisateur</li>";
echo "</ol>";

echo "<p><strong>Note :</strong> Assurez-vous que l'utilisateur admin a la permission 'admin.rank'</p>";
?> 