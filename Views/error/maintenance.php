<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - GeoFound</title>
    <script src="https:
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png">
</head>
<body class="bg-[
    <div class="text-center max-w-md mx-auto px-4">
        <div class="text-8xl mb-6">🔧</div>
        <h1 class="text-3xl font-bold mb-4">Page en Maintenance</h1>
        <p class="text-gray-300 mb-8">
            <?php 
            $maintenance = \App\Models\Permission::getPageMaintenance($_SERVER['REQUEST_URI']);
            echo htmlspecialchars($maintenance['maintenance_message'] ?? 'Cette page est temporairement indisponible pour maintenance.');
            ?>
        </p>
        <div class="space-y-3">
            <button onclick="window.history.back()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                Retour
            </button>
            <br>
            <a href="/" class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                Accueil
            </a>
            <br>
            <button type="button" onclick="document.getElementById('authentication-modal').classList.remove('hidden');" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                Se connecter
            </button>
        </div>
    </div>
    <?php include_once __DIR__ . '/../layouts/modal.php'; ?>
    <script src="assets/js/modal.js"></script>
</body>
</html> 