<?php
/**
 * Script pour réinitialiser le mot de passe de l'utilisateur mod
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Helpers\Database;

echo "=== Réinitialisation du mot de passe ===\n";

try {
    $db = Database::getConnection();
    
    // Nouveau mot de passe simple pour test
    $newPassword = 'test123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Mettre à jour le mot de passe
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE pseudo = 'mod'");
    $stmt->execute([$hashedPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Mot de passe mis à jour avec succès\n";
        echo "Nouveau mot de passe: $newPassword\n";
        echo "Hash: " . substr($hashedPassword, 0, 20) . "...\n\n";
        
        echo "Tu peux maintenant te connecter avec:\n";
        echo "- Pseudo: mod\n";
        echo "- Email: alexandre.queyroi1@gmail.com\n";
        echo "- Mot de passe: $newPassword\n";
    } else {
        echo "❌ Erreur lors de la mise à jour du mot de passe\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
} 