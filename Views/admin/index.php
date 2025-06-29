<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="container mx-auto px-4 py-8">
    
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Tableau de bord d'administration</h1>
        <p class="text-gray-400">Gérez votre plateforme GeoFound depuis ce panneau central</p>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                    <iconify-icon icon="tabler:users" class="text-blue-400" width="24" height="24"></iconify-icon>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Utilisateurs</p>
                    <p class="text-2xl font-semibold text-white" id="stats-users">-</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                    <iconify-icon icon="tabler:file-text" class="text-green-400" width="24" height="24"></iconify-icon>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Posts</p>
                    <p class="text-2xl font-semibold text-white" id="stats-posts">-</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                    <iconify-icon icon="tabler:message-circle" class="text-yellow-400" width="24" height="24"></iconify-icon>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Messages</p>
                    <p class="text-2xl font-semibold text-white" id="stats-messages">-</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                    <iconify-icon icon="tabler:shield" class="text-purple-400" width="24" height="24"></iconify-icon>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Pages en maintenance</p>
                    <p class="text-2xl font-semibold text-white" id="stats-maintenance">-</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4">Actions rapides</h2>
            <div class="space-y-3">
                <?php if (in_array('admin.users', $userPermissions) || in_array('*', $userPermissions)): ?>
                <button onclick="window.location.href='/admin/users'" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:user-plus" class="text-blue-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Gérer les utilisateurs</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
                <?php endif; ?>

                <?php if (in_array('admin.permissions', $userPermissions) || in_array('*', $userPermissions)): ?>
                <button onclick="window.location.href='/admin/permissions'" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:shield-lock" class="text-green-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Gérer les permissions</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
                <?php endif; ?>

                <?php if (in_array('admin.rank', $userPermissions) || in_array('*', $userPermissions)): ?>
                <button onclick="window.location.href='/admin/rank'" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:crown" class="text-purple-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Gérer les rôles</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
                <?php endif; ?>

                <?php if (in_array('admin.maintenance', $userPermissions) || in_array('*', $userPermissions)): ?>
                <button onclick="window.location.href='/admin/maintenance'" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:tools" class="text-yellow-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Gérer la maintenance</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
                <?php endif; ?>

                <?php if (in_array('admin.rewards', $userPermissions) || in_array('*', $userPermissions)): ?>
                <button onclick="window.location.href='/admin/rewards'" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:trophy" class="text-yellow-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Gérer les récompenses</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
                <?php endif; ?>

                <button onclick="toggleQuickActions()" class="w-full flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:bolt" class="text-purple-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Actions rapides</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-gray-400" width="16" height="16"></iconify-icon>
                </button>
            </div>
        </div>

        
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4">Activité récente</h2>
            <div class="space-y-3" id="recent-activity">
                <div class="flex items-center p-3 bg-gray-700 rounded-lg">
                    <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                    <div class="flex-1">
                        <p class="text-sm text-white">Chargement...</p>
                        <p class="text-xs text-gray-400">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (in_array('admin.users', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-blue-500 transition cursor-pointer" onclick="window.location.href='/admin/users'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                    <iconify-icon icon="tabler:users" class="text-blue-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Gestion des utilisateurs</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Gérez les comptes utilisateurs, les rangs et les permissions individuelles.</p>
            <div class="flex items-center text-blue-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin.permissions', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-green-500 transition cursor-pointer" onclick="window.location.href='/admin/permissions'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                    <iconify-icon icon="tabler:shield-lock" class="text-green-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Gestion des permissions</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Créez et gérez les permissions, rangs et accès aux pages.</p>
            <div class="flex items-center text-green-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin.rank', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-purple-500 transition cursor-pointer" onclick="window.location.href='/admin/rank'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                    <iconify-icon icon="tabler:crown" class="text-purple-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Gestion des rôles</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Gérez les rôles des utilisateurs (admin, modérateur, utilisateur).</p>
            <div class="flex items-center text-purple-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin.maintenance', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-yellow-500 transition cursor-pointer" onclick="window.location.href='/admin/maintenance'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                    <iconify-icon icon="tabler:tools" class="text-yellow-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Maintenance</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Configurez la maintenance des pages et gérez les accès.</p>
            <div class="flex items-center text-yellow-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin.reports', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-red-500 transition cursor-pointer" onclick="window.location.href='/admin/reports'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-red-500 bg-opacity-20">
                    <iconify-icon icon="tabler:flag" class="text-red-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Signalements</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Gérez les signalements et modérez le contenu de la plateforme.</p>
            <div class="flex items-center text-red-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin.rewards', $userPermissions) || in_array('*', $userPermissions)): ?>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-yellow-500 transition cursor-pointer" onclick="window.location.href='/admin/rewards'">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                    <iconify-icon icon="tabler:trophy" class="text-yellow-400" width="24" height="24"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-white ml-3">Gestion des Récompenses</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Créez et gérez les récompenses, badges et objets physiques.</p>
            <div class="flex items-center text-yellow-400 text-sm">
                <span>Accéder</span>
                <iconify-icon icon="tabler:arrow-right" class="ml-2" width="16" height="16"></iconify-icon>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<div id="quick-actions-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-white mb-4">Actions rapides</h3>
            <div class="space-y-3">
                <button onclick="quickMaintenanceCurrent()" class="w-full text-left p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:tools" class="text-yellow-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Maintenance de cette page</span>
                    </div>
                </button>
                <button onclick="quickPermissionPublic()" class="w-full text-left p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:world" class="text-green-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Permission publique</span>
                    </div>
                </button>
                <button onclick="quickPermissionLogged()" class="w-full text-left p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:user-check" class="text-blue-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Permission connecté</span>
                    </div>
                </button>
                <button onclick="quickPermissionAdmin()" class="w-full text-left p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <div class="flex items-center">
                        <iconify-icon icon="tabler:shield" class="text-purple-400 mr-3" width="20" height="20"></iconify-icon>
                        <span class="text-white">Permission admin</span>
                    </div>
                </button>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeQuickActions()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAdminStats();
    loadRecentActivity();
});

async function loadAdminStats() {
    try {
        const response = await fetch('/api/admin/stats');
        const stats = await response.json();
        
        document.getElementById('stats-users').textContent = stats.users || 0;
        document.getElementById('stats-posts').textContent = stats.posts || 0;
        document.getElementById('stats-messages').textContent = stats.messages || 0;
        document.getElementById('stats-maintenance').textContent = stats.maintenance || 0;
    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
    }
}

async function loadRecentActivity() {
    try {
        const response = await fetch('/api/admin/activity');
        const activities = await response.json();
        
        const container = document.getElementById('recent-activity');
        container.innerHTML = '';
        
        activities.forEach(activity => {
            const div = document.createElement('div');
            div.className = 'flex items-center p-3 bg-gray-700 rounded-lg';
            div.innerHTML = `
                <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                <div class="flex-1">
                    <p class="text-sm text-white">${activity.description}</p>
                    <p class="text-xs text-gray-400">${new Date(activity.created_at).toLocaleString()}</p>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Erreur lors du chargement de l\'activité:', error);
    }
}

function toggleQuickActions() {
    document.getElementById('quick-actions-modal').classList.remove('hidden');
}

function closeQuickActions() {
    document.getElementById('quick-actions-modal').classList.add('hidden');
}

async function quickMaintenanceCurrent() {
    try {
        await fetch('/api/admin/maintenance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                page_path: window.location.pathname,
                page_name: document.title,
                is_maintenance: true,
                message: 'Page en maintenance'
            })
        });
        closeQuickActions();
        showNotification('Maintenance activée pour cette page', 'success');
    } catch (error) {
        showNotification('Erreur lors de l\'activation de la maintenance', 'error');
    }
}

async function quickPermissionPublic() {
    try {
        await fetch('/api/admin/page-permissions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                page_path: window.location.pathname,
                permission_id: 'public'
            })
        });
        closeQuickActions();
        showNotification('Permission publique ajoutée', 'success');
    } catch (error) {
        showNotification('Erreur lors de l\'ajout de la permission', 'error');
    }
}

async function quickPermissionLogged() {
    try {
        await fetch('/api/admin/page-permissions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                page_path: window.location.pathname,
                permission_id: 'logged'
            })
        });
        closeQuickActions();
        showNotification('Permission connecté ajoutée', 'success');
    } catch (error) {
        showNotification('Erreur lors de l\'ajout de la permission', 'error');
    }
}

async function quickPermissionAdmin() {
    try {
        await fetch('/api/admin/page-permissions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                page_path: window.location.pathname,
                permission_id: 'admin.access'
            })
        });
        closeQuickActions();
        showNotification('Permission admin ajoutée', 'success');
    } catch (error) {
        showNotification('Erreur lors de l\'ajout de la permission', 'error');
    }
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'} text-white`;
    toast.textContent = message;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 