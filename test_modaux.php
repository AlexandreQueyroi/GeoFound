<?php
require_once __DIR__ . '/vendor/autoload.php';

// Démarrer la session
session_start();

// Simuler un utilisateur connecté
$_SESSION['user_id'] = 1;

echo "=== Test des modaux de messagerie ===\n";

// Test 1: Vérifier que le contrôleur MessageController fonctionne
echo "\n1. Test du MessageController...\n";
try {
    $messageController = new \App\Controllers\MessageController();
    echo "✓ MessageController instancié avec succès\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}

// Test 2: Vérifier que le contrôleur FriendController fonctionne
echo "\n2. Test du FriendController...\n";
try {
    $friendController = new \App\Controllers\FriendController();
    echo "✓ FriendController instancié avec succès\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}

// Test 3: Vérifier les routes
echo "\n3. Test des routes...\n";
$routes = require __DIR__ . '/config/routes.php';
$requiredRoutes = ['/friend/add', '/friend/accept', '/friend/refuse', '/friend/requests'];
foreach ($requiredRoutes as $route) {
    if (isset($routes[$route])) {
        echo "✓ Route $route existe\n";
    } else {
        echo "✗ Route $route manquante\n";
    }
}

// Test 4: Vérifier les méthodes du modèle Friend
echo "\n4. Test des méthodes Friend...\n";
try {
    $friends = \App\Models\Friend::getFriends(1);
    echo "✓ getFriends() fonctionne\n";
    
    $requests = \App\Models\Friend::getRequests(1);
    echo "✓ getRequests() fonctionne\n";
    
    echo "✓ Toutes les méthodes Friend fonctionnent\n";
} catch (Exception $e) {
    echo "✗ Erreur dans les méthodes Friend: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test ===\n";
echo "\nPour tester les modaux :\n";
echo "1. Va sur /message/inbox\n";
echo "2. Clique sur 'Ajouter un ami' ou 'Demandes d'amis'\n";
echo "3. Les modaux Flowbite doivent s'ouvrir\n"; 