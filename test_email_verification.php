<?php
/**
 * Script de test pour le système de validation d'email
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Models\EmailVerification;
use App\Helpers\EmailSender;
use App\Helpers\Database;

echo "=== Test du système de validation d'email ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Test 1: Vérifier la connexion à la base de données
    echo "1. Test de connexion à la base de données...\n";
    $db = Database::getConnection();
    echo "✅ Connexion à la base de données réussie\n\n";
    
    // Test 2: Vérifier que la table email_verification_tokens existe
    echo "2. Vérification de la table email_verification_tokens...\n";
    $stmt = $db->query("SHOW TABLES LIKE 'email_verification_tokens'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table email_verification_tokens existe\n\n";
    } else {
        echo "❌ Table email_verification_tokens n'existe pas\n\n";
        exit(1);
    }
    
    // Test 3: Vérifier que la colonne email_verified existe dans users
    echo "3. Vérification de la colonne email_verified...\n";
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Colonne email_verified existe\n\n";
    } else {
        echo "❌ Colonne email_verified n'existe pas\n\n";
        exit(1);
    }
    
    // Test 4: Créer un utilisateur de test
    echo "4. Création d'un utilisateur de test...\n";
    $testEmail = 'test@example.com';
    $testUsername = 'testuser_' . time();
    $testPassword = password_hash('testpass123', PASSWORD_DEFAULT);
    
    // Supprimer l'utilisateur de test s'il existe
    $stmt = $db->prepare("DELETE FROM users WHERE email = ? OR pseudo = ?");
    $stmt->execute([$testEmail, $testUsername]);
    
    // Créer l'utilisateur de test
    $stmt = $db->prepare("
        INSERT INTO users (pseudo, email, password, email_verified) 
        VALUES (?, ?, ?, FALSE)
    ");
    $stmt->execute([$testUsername, $testEmail, $testPassword]);
    $userId = $db->lastInsertId();
    echo "✅ Utilisateur de test créé (ID: $userId)\n\n";
    
    // Test 5: Générer un token de validation
    echo "5. Génération d'un token de validation...\n";
    $token = EmailVerification::generateToken($userId);
    echo "✅ Token généré: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 6: Vérifier que le token existe
    echo "6. Vérification de l'existence du token...\n";
    $hasToken = EmailVerification::hasValidToken($userId);
    if ($hasToken) {
        echo "✅ Token valide trouvé\n\n";
    } else {
        echo "❌ Token non trouvé\n\n";
        exit(1);
    }
    
    // Test 7: Valider le token
    echo "7. Validation du token...\n";
    $validatedUserId = EmailVerification::validateToken($token);
    if ($validatedUserId == $userId) {
        echo "✅ Token validé avec succès\n\n";
    } else {
        echo "❌ Échec de la validation du token\n\n";
        exit(1);
    }
    
    // Test 8: Vérifier que l'email est marqué comme vérifié
    echo "8. Vérification du statut email_verified...\n";
    $stmt = $db->prepare("SELECT email_verified FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user && $user['email_verified']) {
        echo "✅ Email marqué comme vérifié\n\n";
    } else {
        echo "❌ Email non marqué comme vérifié\n\n";
        exit(1);
    }
    
    // Test 9: Nettoyer les données de test
    echo "9. Nettoyage des données de test...\n";
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    echo "✅ Données de test supprimées\n\n";
    
    echo "🎉 Tous les tests sont passés avec succès !\n";
    echo "Le système de validation d'email fonctionne correctement.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
} 