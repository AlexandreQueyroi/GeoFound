<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geofound</title>
    <script src="https://code.iconify.design/3/3.0.0/iconify.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <link rel="icon" type="image/x-icon" href="img/logo.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="switchmode.js" defer></script>
</head>


<body class="flex flex-col min-h-screen bg-[#0A0A23]">
    <?php include_once(__DIR__ . '/../modal.php'); ?>
    <?php var_dump($_SESSION); ?>
    <header class="bg-[#081225] text-white p-3">
        <div class="container mx-auto flex justify-center items-center text-center text-2xl">
            <a href="#" class="flex items-center text-3xl font-semibold">
                <img src="../img/logo.png" alt="Logo" class="w-16 h-16 mr-4">
            </a>

            <nav>
                <div class="flex items-center space-x-9 text-2xl font-bold">
                    <span class="border-2 border-white h-6"></span>
                    <a href="#" class="hover:text-gray-400">Accueil</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="#" class="hover:text-gray-400">Explorer</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="#" class="hover:text-gray-400">Récompenses</a>
                    <span class="border-2 border-white h-6"></span>
                    <?php
                    if (empty($_SESSION['user'])) {
                        echo '<a href="#" class="hover:text-gray-400"
                            data-modal-target="authentication-modal"
                            data-modal-toggle="authentication-modal">Messages</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo '<a href="#" class="hover:text-gray-400"
                            data-modal-target="authentication-modal"
                            data-modal-toggle="authentication-modal">Profil</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo '<a href="#" class="">';
                        echo '<span class="iconify w-8 h-8 text-white hover:text-gray-400" data-icon="tabler:bell"></span>';
                        echo '</a>';
                        echo '<button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                            Connexion
                            </button>';
                    } else {
                        echo '<a href="#" class="hover:text-gray-400">Messages</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo '<a href="#" class="hover:text-gray-400">Profil</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo "<form action='/action/userConnection.php' method='post'>";
                        echo "<input type='hidden' name='disconnect' value='true'>";
                        echo "<button type='submit' name='disconnectBtn' class='bg-gray-500 text-white p-2 rounded hover:bg-red-400 cursor-pointer'>Déconnexion</button>";
                        echo "</form>";
                    }
                    ?>
                </div>
            </nav>
        </div>
    </header>

    <container class="flex-grow max-1000px mx-auto">