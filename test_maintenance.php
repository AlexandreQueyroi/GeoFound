<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Permission;
use App\Models\Rank;

// Simuler une session admin
session_start();
$_SESSION['user_id'] = 1;

echo "Test des permissions pour l'utilisateur admin (ID: 1)\n";
echo "==================================================\n\n";

// Test 1: Vérifier les permissions du rang admin
$rank = Rank::getByName('admin');
echo "Rang admin: " . json_encode($rank) . "\n\n";

// Test 2: Vérifier les permissions spécifiques
$permissions = ['admin.access', 'admin.maintenance', 'admin.users', '*'];
foreach ($permissions as $perm) {
    $hasPerm = Permission::hasPermission(1, $perm);
    echo "Permission '$perm': " . ($hasPerm ? 'OUI' : 'NON') . "\n";
}

echo "\nTest de la méthode getMaintenancePages:\n";
echo "=======================================\n";
$maintenancePages = Permission::getMaintenancePages();
echo "Pages en maintenance: " . json_encode($maintenancePages) . "\n";

echo "\nTest de création d'une page en maintenance:\n";
echo "===========================================\n";
$result = Permission::setPageMaintenance('/test', 'Page de test', true, 'Test de maintenance');
echo "Résultat de création: " . ($result ? 'SUCCÈS' : 'ÉCHEC') . "\n";

$maintenancePages = Permission::getMaintenancePages();
echo "Pages en maintenance après création: " . json_encode($maintenancePages) . "\n"; 