<?php
include_once(__DIR__ . '/build/header.php');
?>

<?php
$status = isset($_GET['status']) ? $_GET['status'] : null;

echo '<div class="flex items-center justify-center min-h-screen">';
echo '<div class="p-6 bg-white rounded-lg shadow-md text-center">';

if ($status === 'success') {
    echo '<p class="text-green-600 font-semibold text-lg">Création de compte effectué, demande de validation via la boite mail (penser à regarder dans les spams).</p>';
} elseif ($status === 'alreadyexist') {
    echo '<p class="text-yellow-600 font-semibold text-lg">Ce compte existe déjà et est déjà vérifié.</p>';
} else {
    echo '<p class="text-red-600 font-semibold text-lg">Création de compte échoué.</p>';
}

echo '</div>';
echo '</div>';
?>

<?php
include_once(__DIR__ . '/build/footer.php');