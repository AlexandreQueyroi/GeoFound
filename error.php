<?php
$errorType = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'Erreur inconnue';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Aucun message fourni.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - <?php echo $errorType; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-100 text-red-800 flex items-center justify-center min-h-screen">
    <div class="bg-red-200 border border-red-300 rounded-lg p-6 text-center shadow-md">
        <img src="/path/to/logo.png" alt="Logo" class="mx-auto mb-4 w-24 h-24">
        <h1 class="text-2xl font-bold"><?php echo $errorType; ?></h1>
        <p class="mt-4"><?php echo $message; ?></p>
    </div>
</body>
</html>
