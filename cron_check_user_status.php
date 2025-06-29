<?php

chdir(__DIR__);


require_once 'vendor/autoload.php';


require_once 'Helpers/UserStatusManager.php';

use App\Helpers\UserStatusManager;
use App\Models\EmailVerification;

echo "=== Vérification automatique des statuts utilisateurs ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    
    $inactiveCount = UserStatusManager::checkAndUpdateUserStatuses();
    
    if ($inactiveCount > 0) {
        echo "✅ $inactiveCount utilisateur(s) désactivé(s) pour inactivité\n";
    } else {
        echo "✅ Aucun utilisateur à désactiver\n";
    }
    
    
    EmailVerification::cleanupExpiredTokens();
    echo "✅ Tokens de validation d'email expirés nettoyés\n";
    
    echo "\nVérification terminée avec succès.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la vérification: " . $e->getMessage() . "\n";
    
    
    $logMessage = date('[d/m/Y H:i:s] ') . "Erreur cron: " . $e->getMessage() . "\n";
    file_put_contents('storage/logs/cron_errors.log', $logMessage, FILE_APPEND);
} 