<?php
/**
 * Script de test pour la configuration SMTP
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Helpers\EmailSender;
use App\Helpers\Logger;

echo "=== Test de la configuration SMTP ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Test 1: Vérifier que la configuration existe
    echo "1. Vérification du fichier de configuration...\n";
    $configFile = __DIR__ . '/config/email.php';
    if (file_exists($configFile)) {
        $config = include $configFile;
        echo "✅ Fichier de configuration trouvé\n";
        echo "   Host: " . $config['smtp']['host'] . "\n";
        echo "   Port: " . $config['smtp']['port'] . "\n";
        echo "   Username: " . $config['smtp']['username'] . "\n";
        echo "   From: " . $config['smtp']['from_email'] . "\n\n";
    } else {
        echo "❌ Fichier de configuration non trouvé\n\n";
        exit(1);
    }
    
    // Test 2: Vérifier que PHPMailer est installé
    echo "2. Vérification de PHPMailer...\n";
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✅ PHPMailer est installé\n\n";
    } else {
        echo "❌ PHPMailer n'est pas installé\n\n";
        exit(1);
    }
    
    // Test 3: Test d'envoi d'email (si les credentials sont configurés)
    echo "3. Test d'envoi d'email...\n";
    
    // Vérifier si les credentials sont configurés
    if ($config['smtp']['username'] === 'your-email@gmail.com' || 
        $config['smtp']['password'] === 'your-app-password') {
        echo "⚠️  Credentials SMTP non configurés\n";
        echo "   Veuillez modifier le fichier config/email.php avec vos vraies credentials\n\n";
        
        echo "Instructions de configuration :\n";
        echo "1. Modifiez config/email.php\n";
        echo "2. Remplacez 'your-email@gmail.com' par votre email Gmail\n";
        echo "3. Remplacez 'your-app-password' par votre mot de passe d'application Gmail\n";
        echo "4. Pour Gmail, activez l'authentification à 2 facteurs et générez un mot de passe d'application\n\n";
        
        echo "Test terminé (configuration requise)\n";
        exit(0);
    }
    
    // Test d'envoi réel
    $testEmail = 'test@example.com';
    $testUsername = 'TestUser';
    $testToken = 'test-token-123';
    
    $result = EmailSender::sendVerificationEmail($testEmail, $testUsername, $testToken);
    
    if ($result) {
        echo "✅ Email de test envoyé avec succès\n";
        echo "   Destinataire: $testEmail\n\n";
    } else {
        echo "❌ Échec de l'envoi de l'email de test\n\n";
        exit(1);
    }
    
    echo "🎉 Configuration SMTP validée avec succès !\n";
    echo "Le système d'envoi d'emails est prêt à être utilisé.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
} 