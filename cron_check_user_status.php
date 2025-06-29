<?php
/**
 * Script cron pour vérifier et mettre à jour automatiquement les statuts des utilisateurs
 * À exécuter toutes les heures : 0 * * * * /usr/bin/php /var/www/geofound/cron_check_user_status.php
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

// Inclure le gestionnaire de statut
require_once 'Helpers/UserStatusManager.php';

use App\Helpers\UserStatusManager;
use App\Models\EmailVerification;

echo "=== Vérification automatique des statuts utilisateurs ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Vérifier et mettre à jour les statuts
    $inactiveCount = UserStatusManager::checkAndUpdateUserStatuses();
    
    if ($inactiveCount > 0) {
        echo "✅ $inactiveCount utilisateur(s) désactivé(s) pour inactivité\n";
    } else {
        echo "✅ Aucun utilisateur à désactiver\n";
    }
    
    // Nettoyer les tokens de validation d'email expirés
    EmailVerification::cleanupExpiredTokens();
    echo "✅ Tokens de validation d'email expirés nettoyés\n";
    
    echo "\nVérification terminée avec succès.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la vérification: " . $e->getMessage() . "\n";
    
    // Log de l'erreur
    $logMessage = date('[d/m/Y H:i:s] ') . "Erreur cron: " . $e->getMessage() . "\n";
    file_put_contents('storage/logs/cron_errors.log', $logMessage, FILE_APPEND);
} 