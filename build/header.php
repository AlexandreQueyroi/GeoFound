<?php
session_start();
$_SESSION['last_url'] = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($_SESSION['last_url'], '?') !== false) {
    $url = strstr($_SESSION['last_url'], '?', true);
} else {
    $url = $_SESSION['last_url'];
}
if (!isset($_SESSION['id']) && ($url != "/" && $url != "/accountCreated" && $url != "/verify")) {
    echo "<script>console.log('not logged');</script>";
    echo "<script>console.log('url : " . $url . "');</script>";
    header('Location: /action/userConnection.php');
    exit();
} else {
    echo "<script>console.log('logged');</script>";
}
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
    <?php include_once(__DIR__ . '/../api/log.php'); ?>
    <?php include_once(__DIR__ . '/../modal.php'); ?>
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col space-y-2"></div>
    <p class="text-white">
        <?php var_dump($_SESSION); ?>
    </p>
    <header class="bg-[#081225] text-white p-3">
        <div class="container mx-auto flex justify-center items-center text-center text-2xl">
            <a href="#" class="flex items-center text-3xl font-semibold">
                <img src="../img/logo.png" alt="Logo" class="w-16 h-16 mr-4">
            </a>

            <nav>
                <div class="flex items-center space-x-9 text-2xl font-bold">
                    <span class="border-2 border-white h-6"></span>
                    <a href="/" class="hover:text-gray-400">Accueil</a>
                    <span class="border-2 border-white h-6"></span>
                    <a href="#" class="hover:text-gray-400">Explorer</a>
                    <span class="border-2 border-white h-6"></span>
                    <?php
                    if (empty($_SESSION['user'])) {
                        echo '<a href="#" class="hover:text-gray-400"
                            data-modal-target="authentication-modal"
                            data-modal-toggle="authentication-modal">Récompenses</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
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
                        echo '<a href="/reward" class="hover:text-gray-400">Récompenses</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo '<a href="/me/inbox" class="hover:text-gray-400">Messages</a>';
                        echo '<span class="border-2 border-white h-6"></span>';
                        echo '<a href="/me/" class="hover:text-gray-400">Profil</a>';
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
<script>
function showToast(message, pseudo) {
    const toast = document.createElement('div');
    toast.className = 'bg-blue-700 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 animate-fade-in-up';
    toast.innerHTML = `<span class='font-bold'>${pseudo}</span><span class='ml-2'>: ${message}</span>`;
    document.getElementById('toast-container').appendChild(toast);
    document.getElementById('notif-sound').play();
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}
const style = document.createElement('style');
style.innerHTML = `
@keyframes fade-in-up {from {opacity:0;transform:translateY(20px);}to {opacity:1;transform:translateY(0);}}
.animate-fade-in-up {animation: fade-in-up 0.3s;}
#toast-container > div {transition: opacity 0.5s;}
}`;
document.head.appendChild(style);

let lastNotifiedMsgId = null;
function pollLastMessage() {
    fetch('/api/last_message.php')
        .then(r => r.json())
        .then(msg => {
            if (msg && msg.id && msg.id !== lastNotifiedMsgId) {
                showToast(msg.content, msg.pseudo);
                lastNotifiedMsgId = msg.id;
            }
        });
}
setInterval(pollLastMessage, 7000);
pollLastMessage();
</script>