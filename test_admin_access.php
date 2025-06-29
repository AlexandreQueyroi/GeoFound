<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Permission;
use App\Helpers\Database;

echo "🔍 Test d'accès au panel administrateur\n\n";

// Simuler une session utilisateur admin
$_SESSION['user_id'] = 1;
$_SESSION['user'] = 'admin';

echo "👤 Utilisateur connecté: {$_SESSION['user']} (ID: {$_SESSION['user_id']})\n\n";

// Test des permissions
$isAdmin = Permission::isAdmin($_SESSION['user_id']);
$hasAdminAccess = Permission::hasPermission($_SESSION['user_id'], 'admin.access');

echo "🔐 Permissions:\n";
echo "   Permission '*' (admin): " . ($isAdmin ? "✅ OUI" : "❌ NON") . "\n";
echo "   Permission 'admin.access': " . ($hasAdminAccess ? "✅ OUI" : "❌ NON") . "\n\n";

// Test de la méthode index du AdminController
echo "🎯 Test de la méthode AdminController::index():\n";

try {
    // Simuler l'appel à la méthode index
    if (!isset($_SESSION['user_id']) || !Permission::hasPermission($_SESSION['user_id'], 'admin.access')) {
        echo "   ❌ Accès refusé - Redirection vers /error/403\n";
    } else {
        echo "   ✅ Accès autorisé\n";
        
        // Récupérer les permissions pour l'affichage
        $permissions = Permission::getUserPermissions($_SESSION['user_id']);
        $userPermissions = array_map(function($perm) {
            return $perm['name'];
        }, $permissions);
        
        // Ajouter les permissions du rang
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
        
        echo "   📋 Permissions disponibles: " . implode(', ', $userPermissions) . "\n";
        
        // Vérifier si la vue existe
        $viewPath = __DIR__ . '/Views/admin/index.php';
        if (file_exists($viewPath)) {
            echo "   ✅ Vue admin/index.php trouvée\n";
        } else {
            echo "   ❌ Vue admin/index.php manquante\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🔧 Diagnostic:\n";
if ($isAdmin) {
    echo "   ✅ L'utilisateur a les permissions d'admin\n";
    echo "   ✅ Le panel admin devrait être accessible\n";
    echo "   🌐 URL: http://votre-domaine.com/admin\n";
} else {
    echo "   ❌ L'utilisateur n'a pas les permissions d'admin\n";
    echo "   🔧 Vérifiez les permissions dans la base de données\n";
}
?> 