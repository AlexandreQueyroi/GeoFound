<?php
use App\Models\Permission;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    try {
        $db = \App\Helpers\Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET connected = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (\Exception $e) {
        error_log('Failed to update user online status: ' . $e->getMessage());
    }
}

$_SESSION['last_url'] = $_SERVER['REQUEST_URI'];

$userPermissions = [];
if (isset($_SESSION['user_id'])) {
    $permissions = Permission::getUserPermissions($_SESSION['user_id']);
    $userPermissions = array_map(function($perm) {
        return $perm['name'];
    }, $permissions);
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
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#081225',
                        secondary: '#1a2234',
                        accent: '#3b82f6',
                    },
                },
            },
            plugins: [
                function({ addUtilities }) {
                    const newUtilities = {
                        '.scrollbar-thin': {
                            'scrollbar-width': 'thin',
                            '&::-webkit-scrollbar': {
                                width: '8px',
                            },
                        },
                        '.scrollbar-thumb-gray-600': {
                            '&::-webkit-scrollbar-thumb': {
                                backgroundColor: '#4B5563',
                                borderRadius: '4px',
                            },
                        },
                        '.scrollbar-track-gray-800': {
                            '&::-webkit-scrollbar-track': {
                                backgroundColor: '#1F2937',
                            },
                        },
                    };
                    addUtilities(newUtilities);
                },
            ],
        };
    </script>
    <style>
        .message-list::-webkit-scrollbar {
            width: 8px;
        }
        .message-list::-webkit-scrollbar-track {
            background: #1F2937;
            border-radius: 4px;
        }
        .message-list::-webkit-scrollbar-thumb {
            background: #4B5563;
            border-radius: 4px;
        }
        .message-list::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }
        
        .modal {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .modal.show {
            display: flex;
            opacity: 1;
        }
        
        .message {
            opacity: 0;
            animation: fadeIn 0.3s ease-in-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.3s;
        }
        #toast-container > div {
            transition: opacity 0.5s;
        }
    </style>
    <script src="assets/js/permissions.js?v=<?php echo time(); ?>" defer></script>
    <?php if (strpos($_SERVER['REQUEST_URI'], 'message') !== false): ?>
    <script src="assets/js/messagerie.js?v=<?=time()?>" defer></script>
    <?php endif; ?>
    
    <script>
        console.log('Header: Test de chargement du script');
        window.addEventListener('load', function() {
            console.log('Header: Page chargée, test de la fonction openPostModal');
            if (typeof openPostModal === 'function') {
                console.log('Header: Fonction openPostModal disponible');
            } else {
                console.log('Header: Fonction openPostModal NON disponible');
            }
        });
    </script>
    
    <script>
        window.userPermissions = <?php echo json_encode($userPermissions); ?>;
        console.log('Permissions transmises au JS:', window.userPermissions);
        console.log('Utilisateur connecté:', <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>);
        console.log('User ID:', <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>);
    </script>
</head>
<body class="flex flex-col min-h-screen bg-[#0A0A23]">
    <?php include_once __DIR__ . '/modal.php'; ?>
    
    
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col space-y-2"></div>
    
    <audio id="notif-sound" preload="auto">
        <source src="assets/sounds/notification.mp3" type="audio/mpeg">
    </audio>
    
    <header class="bg-[#081225] text-white shadow">
        <div class="container mx-auto flex items-center justify-between py-2 px-4">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center space-x-2">
                    <img src="assets/img/logo.png" alt="Logo" class="w-12 h-12">
                    <span class="text-2xl font-bold hidden sm:block">GeoFound</span>
                </a>
                <nav>
                    <ul class="flex flex-wrap gap-6 text-lg font-semibold items-center">
                        <li><a href="/" class="hover:text-blue-400 transition">Accueil</a></li>
                        <li><a href="/explorer" class="hover:text-blue-400 transition">Explorer</a></li>
                        <li><a href="/post" class="hover:text-blue-400 transition">Posts</a></li>
                        <li><a href="/reward" class="hover:text-blue-400 transition">Récompenses</a></li>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="/message/inbox" class="hover:text-blue-400 transition">Messages</a></li>
                            <li><a href="/me" class="hover:text-blue-400 transition">Profil</a></li>
                            <?php if (in_array('admin.stats', $userPermissions) || in_array('*', $userPermissions)): ?>
                                <li><a href="/admin/stats" class="hover:text-blue-400 transition">Statistiques</a></li>
                            <?php endif; ?>
                            <?php if (in_array('admin.glpi', $userPermissions) || in_array('*', $userPermissions)): ?>
                                <li><a href="/admin/glpi" class="hover:text-blue-400 transition">GLPI</a></li>
                            <?php endif; ?>
                            <?php if (in_array('admin.access', $userPermissions) || in_array('*', $userPermissions)): ?>
                                <li><a href="/admin" class="hover:text-blue-400 transition">Administration</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <button class="relative group" aria-label="Notifications">
                    <iconify-icon icon="tabler:bell" width="32" height="32" class="hover:text-blue-400 transition"></iconify-icon>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full px-1 hidden group-hover:block">!</span>
                </button>
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-[#1a2234] transition group" id="user-menu-button" type="button">
                            <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                            <iconify-icon icon="tabler:chevron-down" width="20" height="20"></iconify-icon>
                        </button>
                        <div class="absolute right-0 mt-0 w-48 bg-[#1a2234] rounded-lg shadow-lg py-2 z-50 hidden group-hover:block" id="user-menu-dropdown" style="min-width: 180px; padding-top:0; padding-bottom:0;">
                            <a href="/me" class="block px-4 py-2 hover:bg-blue-600 transition">Mon profil</a>
                            <a href="/message/inbox" class="block px-4 py-2 hover:bg-blue-600 transition">Messagerie</a>
                            <a href="/user/settings" class="block px-4 py-2 hover:bg-blue-600 transition">Paramètres</a>
                            <form action="/auth/logout" method="post">
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-600 transition">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Connexion
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-1">
    
    <!--
    <script>
    function showToast(message, pseudo) { ... }
    function pollLastMessage() { ... }
    </script>
    -->

    </main>

    <?php if (isset($_SESSION['user']) && (in_array('post.create', $userPermissions) || in_array('*', $userPermissions))): ?>
    <button 
        type="button"
        data-modal-target="post-modal" 
        data-modal-toggle="post-modal"
        class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white w-14 h-14 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center z-30 hover:scale-110"
        title="Créer un nouveau post"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    
    <?php
    if (!isset($_SESSION['user']) && isset($_SESSION['show_login_modal']) && $_SESSION['show_login_modal']) {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('authentication-modal');
                if (modalEl) {
                    const modal = new Modal(modalEl);
                    modal.show();
                }
            });
        </script>";
        unset($_SESSION['show_login_modal']);
    }
    ?>

    <script>
        function submitPostForm() {
            const form = document.getElementById('postForm');
            if (form) {
                const formData = new FormData(form);
                if (!formData.get('latitude') || !formData.get('longitude')) {
                    formData.set('latitude', '48.8566');
                    formData.set('longitude', '2.3522');
                }
                
                fetch('/post/create', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = document.getElementById('post-modal');
                        if (modal) {
                            const modalInstance = new Modal(modal);
                            modalInstance.hide();
                        }
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + (data.message || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    console.error('Erreur de soumission:', error);
                    alert('Une erreur technique est survenue.');
                });
            }
        }
    </script>
</body>
</html>
