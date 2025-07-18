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
    $_SESSION['permissions'] = $userPermissions;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geofound</title>
    <script src="https:
    <link href="https:
    <link rel="icon" type="image/x-icon" href="/assets/img/logo.png">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https:
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
        
        .manager-button {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .user-menu-group:hover .manager-button {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    <script src="/assets/js/permissions.js?v=<?php echo time(); ?>" defer></script>
    <?php if (strpos($_SERVER['REQUEST_URI'], 'message') !== false): ?>
    <script src="/assets/js/messagerie.js?v=<?=time()?>" defer></script>
    <?php endif; ?>
    <script src="/assets/js/post-modal.js?v=<?php echo time(); ?>" defer></script>
    
    <script>
        window.addEventListener('load', function() {
            if (typeof openPostModal === 'function') {
            } else {
            }
        });
    </script>
    
    <script>
        window.userPermissions = <?php echo json_encode($userPermissions); ?>;
    </script>
</head>
<body class="flex flex-col min-h-screen bg-[#0A0A23]">
    <?php include_once __DIR__ . '/modal.php'; ?>
    
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col space-y-2"></div>
    
    <audio id="notif-sound" preload="auto">
        <source src="/assets/sounds/notification.mp3" type="audio/mpeg">
    </audio>
    
    <header class="bg-[#081225] text-white shadow">
        <div class="container mx-auto flex items-center justify-between py-2 px-4">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center space-x-2">
                    <img src="/assets/img/logo.png" alt="Logo" class="w-12 h-12">
                    <span class="text-2xl font-bold hidden sm:block">GeoFound</span>
                </a>
                <nav>
                    <ul class="flex flex-wrap gap-6 text-lg font-semibold items-center">
                        <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0): ?>
                            
                            <li><a href="/admin" class="hover:text-blue-400 transition">Dashboard</a></li>
                            <li><a href="/admin/users" class="hover:text-blue-400 transition">Utilisateurs</a></li>
                            <li><a href="/admin/rank" class="hover:text-blue-400 transition">Grades</a></li>
                            <li><a href="/admin/permissions" class="hover:text-blue-400 transition">Permissions</a></li>
                            <li><a href="/admin/reports" class="hover:text-blue-400 transition">Signalements</a></li>
                            <li><a href="/admin/maintenance" class="hover:text-blue-400 transition">Maintenance</a></li>
                        <?php else: ?>
                            
                            <li><a href="/" class="hover:text-blue-400 transition">Accueil</a></li>
                            <li><a href="/post" class="hover:text-blue-400 transition">Posts</a></li>
                            <li><a href="/reward" class="hover:text-blue-400 transition">Récompenses</a></li>
                            <?php if (isset($_SESSION['user'])): ?>
                                <li><a href="/me/inbox" class="hover:text-blue-400 transition">Messages</a></li>
                                <li><a href="/me" class="hover:text-blue-400 transition">Profil</a></li>
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
                    <div class="relative group user-menu-group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-[#1a2234] transition group" id="user-menu-button" type="button">
                            <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                            <iconify-icon icon="tabler:chevron-down" width="20" height="20"></iconify-icon>
                        </button>
                        
                        
                        <?php if (in_array('admin.access', $userPermissions) || in_array('*', $userPermissions)): ?>
                        <button 
                            onclick="window.location.href='/admin'" 
                            class="manager-button absolute -top-2 -right-2 bg-purple-600 hover:bg-purple-700 text-white text-xs px-2 py-1 rounded-full shadow-lg transition-all duration-300 z-10"
                            title="Accéder à l'administration"
                        >
                            <iconify-icon icon="tabler:settings" width="12" height="12"></iconify-icon>
                            Manager
                        </button>
                        <?php endif; ?>
                        
                        <div class="absolute right-0 mt-0 w-48 bg-[#1a2234] rounded-lg shadow-lg py-2 z-50 hidden group-hover:block" id="user-menu-dropdown" style="min-width: 180px; padding-top:0; padding-bottom:0;">
                            <a href="/me" class="block px-4 py-2 hover:bg-blue-600 transition">Mon profil</a>
                            <a href="/me/inbox" class="block px-4 py-2 hover:bg-blue-600 transition">Messagerie</a>
                            <a href="/me/edit" class="block px-4 py-2 hover:bg-blue-600 transition">Éditer le profil</a>
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

    </main>

    <?php if (isset($_SESSION['user']) && (in_array('post.create', $userPermissions) || in_array('*', $userPermissions))): ?>
    <button 
        type="button"
        onclick="showPostModal()"
        class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white w-14 h-14 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center z-30 hover:scale-110"
        title="Créer un nouveau post"
    >
        <iconify-icon icon="tabler:plus" width="24" height="24"></iconify-icon>
    </button>
    <?php endif; ?>

    <script src="https:
    
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
        
        function openPostModal() {
            const modal = document.getElementById('post-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
    </script>
</body>
</html>
