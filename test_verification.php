<?php
/**
 * Script de test pour générer un token de validation
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Models\EmailVerification;
use App\Helpers\EmailSender;

echo "=== Test de génération de token ===\n";

try {
    // Récupérer l'ID de l'utilisateur mod
    $db = \App\Helpers\Database::getConnection();
    $stmt = $db->prepare("SELECT id, pseudo, email FROM users WHERE pseudo = 'mod'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "❌ Utilisateur 'mod' non trouvé\n";
        exit(1);
    }
    
    echo "✅ Utilisateur trouvé: {$user['pseudo']} ({$user['email']})\n";
    
    // Générer un nouveau token
    $token = EmailVerification::generateToken($user['id']);
    echo "✅ Token généré: $token\n";
    
    // Construire l'URL de validation
    $verificationUrl = "https://geofound.fr/auth/verify?token=" . $token;
    echo "🔗 URL de validation: $verificationUrl\n\n";
    
    // Envoyer l'email
    $result = EmailSender::sendVerificationEmail($user['email'], $user['pseudo'], $token);
    
    if ($result) {
        echo "✅ Email de validation envoyé avec succès\n";
    } else {
        echo "❌ Échec de l'envoi d'email\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
} 