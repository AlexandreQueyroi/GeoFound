<?php
/**
 * Script de test pour diagnostiquer le système d'email
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Helpers\EmailSender;
use App\Helpers\Logger;

echo "=== Test du système d'email ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Test 1: Vérifier que PHPMailer est installé
    echo "1. Vérification de PHPMailer...\n";
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✅ PHPMailer est installé\n\n";
    } else {
        echo "❌ PHPMailer n'est pas installé\n\n";
        exit(1);
    }
    
    // Test 2: Vérifier la configuration email
    echo "2. Vérification de la configuration email...\n";
    $config = include 'config/email.php';
    if ($config && isset($config['smtp'])) {
        echo "✅ Configuration email trouvée\n";
        echo "   Host: " . $config['smtp']['host'] . "\n";
        echo "   Port: " . $config['smtp']['port'] . "\n";
        echo "   Username: " . $config['smtp']['username'] . "\n";
        echo "   Encryption: " . $config['smtp']['encryption'] . "\n\n";
    } else {
        echo "❌ Configuration email manquante ou invalide\n\n";
        exit(1);
    }
    
    // Test 3: Test de connexion SMTP
    echo "3. Test de connexion SMTP...\n";
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->SMTPDebug = 2; // Activer le debug
        $mail->isSMTP();
        $mail->Host = $config['smtp']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp']['username'];
        $mail->Password = $config['smtp']['password'];
        $mail->SMTPSecure = $config['smtp']['encryption'] === 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp']['port'];
        $mail->CharSet = 'UTF-8';
        
        // Test de connexion sans envoi
        $mail->smtpConnect();
        echo "✅ Connexion SMTP réussie\n\n";
        $mail->smtpClose();
        
    } catch (Exception $e) {
        echo "❌ Erreur de connexion SMTP: " . $e->getMessage() . "\n\n";
        exit(1);
    }
    
    // Test 4: Test d'envoi d'email simple
    echo "4. Test d'envoi d'email...\n";
    $testEmail = 'test@example.com'; // Email de test
    $testUsername = 'TestUser';
    $testToken = 'test_token_123';
    
    $result = EmailSender::sendVerificationEmail($testEmail, $testUsername, $testToken);
    
    if ($result) {
        echo "✅ Email envoyé avec succès\n\n";
    } else {
        echo "❌ Échec de l'envoi d'email\n\n";
        // Vérifier les logs
        echo "Vérification des logs d'erreur...\n";
        $logFile = 'storage/logs/php_errors.log';
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $lines = explode("\n", $logs);
            $recentLogs = array_slice($lines, -10);
            echo "Derniers logs:\n";
            foreach ($recentLogs as $log) {
                if (trim($log)) {
                    echo "  $log\n";
                }
            }
        }
        echo "\n";
    }
    
    echo "🎉 Test du système d'email terminé\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
} 