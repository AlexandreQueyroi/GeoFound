<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geofound</title>
    <script src="https:
    <link href="https:
    <script src="https:

    <link rel="icon" type="image/x-icon" href="<?php __DIR__ ?>"/img/logo.png">

    <script src="https:
    <script src="switchmode.js" defer></script>
</head>

<?php
session_start();
?>

<body class="flex flex-col min-h-screen bg-[
<?php 
    error_reporting(E_ALL);
    ini_set("display_errors", 0);

    echo $undefinedVariable;
?>
    <?php include_once(__DIR__ . '/../modal.php'); ?>
    <p class="text-white"> 
    <?php var_dump($_SESSION); ?>
    </p>
    <header class="bg-[
        <div class="container mx-auto flex justify-center items-center text-center text-2xl">
            <a href="
                <img src="../img/logo.png" alt="Logo" class="w-16 h-16 mr-4">
            </a>

            <nav>
                <div class="flex items-center space-x-9 text-2xl font-bold">
                    <a href="../admin/maintenance.php" class="hover:text-gray-400">Maintenance</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="../admin/user.php" class="hover:text-gray-400">Utilisateurs</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="../admin/rank.php" class="hover:text-gray-400">Rôles</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="../admin/captcha.php" class="hover:text-gray-400">Captcha</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="../admin/log.php" class="hover:text-gray-400">Logs</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="../admin/stat.php" class="hover:text-gray-400">Statistiques</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="
                    <?php
                    if (empty($_SESSION['user'])) {
                        echo '<button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Connexion
                    </button>';
                    } else {

                        echo "<form action='/action/userConnection.php' method='post'>";
                        echo "<input type='hidden' name='disconnect' value='true'>";
                        echo "<button type='submit' name='disconnectBtn'
                            class='bg-gray-500 text-white p-2 rounded hover:bg-red-400 cursor-pointer'>Déconnexion</button>";
                        echo "</form>";
                    }
                    ?>
                </div>
            </nav>
        </div>
    </header>

    <container class="flex-grow max-1000px mx-auto">