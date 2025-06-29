<?php
/**
 * Script de test pour la connexion
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Models\Auth;

echo "=== Test de connexion ===\n";

try {
    // Test avec le pseudo
    echo "1. Test avec le pseudo 'mod'...\n";
    $user = Auth::login('mod', 'test123');
    
    if ($user) {
        echo "✅ Connexion réussie avec le pseudo\n";
        echo "   ID: {$user['id']}\n";
        echo "   Pseudo: {$user['pseudo']}\n";
        echo "   Email: {$user['email']}\n";
        echo "   Email vérifié: " . ($user['email_verified'] ? 'Oui' : 'Non') . "\n";
        echo "   Désactivé: " . ($user['desactivated'] ? 'Oui' : 'Non') . "\n";
    } else {
        echo "❌ Échec de connexion avec le pseudo\n";
    }
    
    echo "\n2. Test avec l'email...\n";
    $user = Auth::login('alexandre.queyroi1@gmail.com', 'test123');
    
    if ($user) {
        echo "✅ Connexion réussie avec l'email\n";
        echo "   ID: {$user['id']}\n";
        echo "   Pseudo: {$user['pseudo']}\n";
        echo "   Email: {$user['email']}\n";
    } else {
        echo "❌ Échec de connexion avec l'email\n";
    }
    
    // Vérifier le hash du mot de passe
    echo "\n3. Vérification du hash du mot de passe...\n";
    $db = \App\Helpers\Database::getConnection();
    $stmt = $db->prepare("SELECT password FROM users WHERE pseudo = 'mod'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Hash du mot de passe: " . substr($result['password'], 0, 20) . "...\n";
        echo "Longueur du hash: " . strlen($result['password']) . " caractères\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
} 