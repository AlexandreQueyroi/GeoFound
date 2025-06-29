<?php
session_start();

// Simuler une session admin
$_SESSION['user_id'] = 8;
$_SESSION['user'] = 'mod';
$_SESSION['username'] = 'mod';
$_SESSION['permissions'] = ['*'];

echo "=== Test de la page users ===\n\n";

echo "Vérification des permissions:\n";
echo "- user_id: " . $_SESSION['user_id'] . "\n";
echo "- permissions: " . implode(', ', $_SESSION['permissions']) . "\n\n";

// Test de l'API
echo "Test de l'API /api/admin/users:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://geofound.fr/api/admin/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "- Code HTTP: $httpCode\n";
echo "- Réponse: " . substr($response, 0, 200) . "...\n\n";

// Test de la page
echo "Test de la page /admin/users:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://geofound.fr/admin/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "- Code HTTP: $httpCode\n";
echo "- Taille réponse: " . strlen($response) . " caractères\n";
echo "- Contient 'Gestion des utilisateurs': " . (strpos($response, 'Gestion des utilisateurs') !== false ? 'OUI' : 'NON') . "\n";
echo "- Contient 'edit-user-modal': " . (strpos($response, 'edit-user-modal') !== false ? 'OUI' : 'NON') . "\n";
echo "- Contient 'renderUsers': " . (strpos($response, 'renderUsers') !== false ? 'OUI' : 'NON') . "\n"; 