<?php
/**
 * Script de test pour vérifier la configuration de l'administration
 * Usage: php test_admin.php
 */

echo "=== Test de configuration de l'administration GeoFound ===\n\n";

// Test 1: Vérifier que les fichiers existent
echo "1. Vérification des fichiers...\n";
$files_to_check = [
    'Controllers/AdminController.php',
    'Views/admin/index.php',
    'Views/admin/users.php',
    'Views/admin/permissions.php',
    'Views/admin/maintenance.php',
    'config/routes.php',
    'Views/layouts/header.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✓ $file\n";
    } else {
        echo "✗ $file (manquant)\n";
    }
}

echo "\n2. Vérification de la syntaxe PHP...\n";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "✓ $file\n";
        } else {
            echo "✗ $file - Erreur de syntaxe\n";
        }
    }
}

echo "\n3. Vérification des routes d'administration...\n";
$routes_content = file_get_contents('config/routes.php');
$admin_routes = [
    '/admin' => 'AdminController::index',
    '/admin/users' => 'AdminController::users',
    '/admin/permissions' => 'AdminController::permissions',
    '/admin/maintenance' => 'AdminController::maintenance',
    '/api/admin/stats' => 'AdminController::apiStats',
    '/api/admin/activity' => 'AdminController::apiActivity',
    '/api/admin/users' => 'AdminController::apiUsers'
];

foreach ($admin_routes as $route => $controller) {
    if (strpos($routes_content, $route) !== false) {
        echo "✓ Route $route configurée\n";
    } else {
        echo "✗ Route $route manquante\n";
    }
}

echo "\n4. Vérification des permissions dans le header...\n";
$header_content = file_get_contents('Views/layouts/header.php');
if (strpos($header_content, 'admin.access') !== false) {
    echo "✓ Permission admin.access détectée dans le header\n";
} else {
    echo "✗ Permission admin.access manquante dans le header\n";
}

if (strpos($header_content, 'manager-button') !== false) {
    echo "✓ Bouton Manager détecté dans le header\n";
} else {
    echo "✗ Bouton Manager manquant dans le header\n";
}

echo "\n5. Vérification des méthodes du contrôleur...\n";
$controller_content = file_get_contents('Controllers/AdminController.php');
$methods = [
    'index',
    'users',
    'permissions',
    'maintenance',
    'apiStats',
    'apiActivity',
    'apiUsers',
    'apiUser',
    'apiToggleUserStatus'
];

foreach ($methods as $method) {
    if (strpos($controller_content, "function $method") !== false) {
        echo "✓ Méthode $method() trouvée\n";
    } else {
        echo "✗ Méthode $method() manquante\n";
    }
}

echo "\n=== Résumé ===\n";
echo "Configuration de l'administration GeoFound terminée !\n";
echo "Pour tester :\n";
echo "1. Connectez-vous avec un compte ayant la permission 'admin.access'\n";
echo "2. Survolez votre nom d'utilisateur pour voir le bouton 'Manager'\n";
echo "3. Accédez à /admin pour le tableau de bord\n";
echo "4. Testez les différentes sections : /admin/users, /admin/permissions, etc.\n";
?> 