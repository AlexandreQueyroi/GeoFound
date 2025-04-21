<?php
$errorCode = $_GET['error'] ?? '404';

$errorMessages = [
    '403' => [
        'title' => "403 - Accès refusé",
        'message' => "Même Indiana Jones n’a pas les droits ici.",
        'icon' => "mdi:door-closed-lock"
    ],
    '404' => [
        'title' => "404 - Page introuvable",
        'message' => "On a cherché partout… mais cette page s’est volatilisée !",
        'icon' => "noto:world-map"
    ],
    '500' => [
        'title' => "500 - Erreur serveur",
        'message' => "Nos serveurs sont partis en vacances 🌴",
        'icon' => "twemoji:fire"
    ],
    'default' => [
        'title' => "$errorCode - Oups !",
        'message' => "Une erreur inconnue est survenue. On appelle les secours 🚁",
        'icon' => "emojione:warning"
    ]
];

$data = $errorMessages[$errorCode] ?? $errorMessages['default'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($data['title']) ?> | GeoFound</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.iconify.design/3/3.0.0/iconify.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <link rel="icon" type="image/x-icon" href="img/logo.png">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#081225] min-h-screen flex items-center justify-center px-4">
  <div class="max-w-md text-center space-y-6 bg-gray-300 p-8 rounded-2xl shadow-xl border border-gray-200">
    <iconify-icon icon="<?= htmlspecialchars($data['icon']) ?>" width="80" height="80" class="mx-auto text-indigo-500"></iconify-icon>
    <h1 class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($data['title']) ?></h1>
    <p class="text-lg text-gray-600"><?= htmlspecialchars($data['message']) ?></p>
    <a href="/" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-300 shadow">
      Retour à la carte
    </a>
    <p class="text-xs text-gray-400 italic">GeoFound™ - On trouve tout... sauf ça.</p>
  </div>
</body>
</html>