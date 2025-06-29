<?php
/**
 * Script de test pour le système complet de création de compte
 */

// Définir le répertoire de base
chdir(__DIR__);

// Inclure l'autoloader
require_once 'vendor/autoload.php';

use App\Helpers\Database;

echo "=== Test du système de création de compte ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Test 1: Vérifier la connexion à la base de données
    echo "1. Test de connexion à la base de données...\n";
    $db = Database::getConnection();
    echo "✅ Connexion à la base de données réussie\n\n";
    
    // Test 2: Vérifier que la table captcha contient des données
    echo "2. Vérification des captchas...\n";
    $stmt = $db->query("SELECT COUNT(*) FROM captcha WHERE enabled = 1");
    $captchaCount = $stmt->fetchColumn();
    if ($captchaCount > 0) {
        echo "✅ $captchaCount captcha(s) disponible(s)\n\n";
    } else {
        echo "❌ Aucun captcha disponible\n\n";
        exit(1);
    }
    
    // Test 3: Vérifier que la table email_verification_tokens existe
    echo "3. Vérification de la table email_verification_tokens...\n";
    $stmt = $db->query("SHOW TABLES LIKE 'email_verification_tokens'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table email_verification_tokens existe\n\n";
    } else {
        echo "❌ Table email_verification_tokens n'existe pas\n\n";
        exit(1);
    }
    
    // Test 4: Vérifier que la colonne email_verified existe
    echo "4. Vérification de la colonne email_verified...\n";
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Colonne email_verified existe\n\n";
    } else {
        echo "❌ Colonne email_verified n'existe pas\n\n";
        exit(1);
    }
    
    // Test 5: Test de l'API captcha
    echo "5. Test de l'API captcha...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/api/captcha");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if ($data && isset($data['question']) && isset($data['answer'])) {
            echo "✅ API captcha fonctionne\n";
            echo "   Question: " . $data['question'] . "\n";
            echo "   Réponse: " . $data['answer'] . "\n\n";
        } else {
            echo "❌ Réponse API captcha invalide\n\n";
            exit(1);
        }
    } else {
        echo "❌ Erreur API captcha (HTTP $httpCode)\n\n";
        exit(1);
    }
    
    // Test 6: Test de la route de création de compte
    echo "6. Test de la route de création de compte...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/auth/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✅ Route /auth/register accessible\n\n";
    } else {
        echo "❌ Erreur route /auth/register (HTTP $httpCode)\n\n";
        exit(1);
    }
    
    echo "🎉 Tous les tests sont passés avec succès !\n";
    echo "Le système de création de compte avec captcha est prêt.\n\n";
    
    echo "Instructions pour tester :\n";
    echo "1. Va sur http://localhost\n";
    echo "2. Clique sur 'Connexion'\n";
    echo "3. Clique sur 'Créer votre compte'\n";
    echo "4. Remplis le formulaire et réponds au captcha\n";
    echo "5. Soumets le formulaire\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
} 