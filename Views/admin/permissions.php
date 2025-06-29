<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-900 rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-white mb-8">Gestion des Permissions</h1>
        
        
        <div class="border-b border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button class="tab-button active text-blue-400 border-b-2 border-blue-400 py-2 px-1 text-sm font-medium" data-tab="permissions">
                    Permissions
                </button>
                <button class="tab-button text-gray-400 border-b-2 border-transparent py-2 px-1 text-sm font-medium hover:text-gray-300" data-tab="ranks">
                    Rangs
                </button>
                <button class="tab-button text-gray-400 border-b-2 border-transparent py-2 px-1 text-sm font-medium hover:text-gray-300" data-tab="users">
                    Permissions Utilisateurs
                </button>
                <button class="tab-button text-gray-400 border-b-2 border-transparent py-2 px-1 text-sm font-medium hover:text-gray-300" data-tab="maintenance">
                    Maintenance
                </button>
            </nav>
        </div>

        
        <div id="permissions-tab" class="tab-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Liste des Permissions</h2>
                <button id="add-permission-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Ajouter une Permission
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-800 rounded-lg">
                    <thead>
                        <tr class="bg-gray-700">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Créée le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="permissions-list" class="bg-gray-800 divide-y divide-gray-700">
                        
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="ranks-tab" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Gestion des Rangs</h2>
                <button id="add-rank-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Ajouter un Rang
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Rangs existants</h3>
                    <div id="ranks-list" class="space-y-3">
                        
                    </div>
                </div>
                
                
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Permissions du rang</h3>
                    <div id="rank-permissions" class="space-y-3">
                        <p class="text-gray-400">Sélectionnez un rang pour voir ses permissions</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div id="users-tab" class="tab-content hidden">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-white mb-4">Permissions Utilisateurs</h2>
                <div class="flex gap-4">
                    <input type="text" id="user-search" placeholder="Rechercher un utilisateur..." class="bg-gray-700 text-white px-4 py-2 rounded-lg flex-1">
                    <button id="search-user-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Rechercher
                    </button>
                </div>
            </div>
            
            <div id="user-permissions-content" class="bg-gray-800 rounded-lg p-6">
                <p class="text-gray-400">Recherchez un utilisateur pour gérer ses permissions</p>
            </div>
        </div>

        
        <div id="maintenance-tab" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Gestion de la Maintenance</h2>
                <button id="add-maintenance-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Ajouter une Page
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-800 rounded-lg">
                    <thead>
                        <tr class="bg-gray-700">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Page</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Maintenance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="maintenance-list" class="bg-gray-800 divide-y divide-gray-700">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div id="permission-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-white mb-4">Ajouter une Permission</h3>
            <form id="permission-form">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nom de la permission</label>
                    <input type="text" id="permission-name" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea id="permission-description" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg h-20"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-permission" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="rank-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-white mb-4">Ajouter un Rang</h3>
            <form id="rank-form">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nom du rang</label>
                    <input type="text" id="rank-name" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Couleur</label>
                    <input type="color" id="rank-color" class="w-full h-10 bg-gray-700 rounded-lg" value="
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-rank" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="maintenance-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-white mb-4">Gérer la Maintenance</h3>
            <form id="maintenance-form">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Chemin de la page</label>
                    <input type="text" id="page-path" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nom de la page</label>
                    <input type="text" id="page-name" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="is-maintenance" class="mr-2">
                        <span class="text-sm font-medium text-gray-300">En maintenance</span>
                    </label>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Message de maintenance</label>
                    <textarea id="maintenance-message" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg h-20"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-maintenance" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.dataset.tab;
            
            
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-blue-400', 'border-blue-400');
                btn.classList.add('text-gray-400', 'border-transparent');
            });
            button.classList.add('active', 'text-blue-400', 'border-blue-400');
            button.classList.remove('text-gray-400', 'border-transparent');
            
            
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            
            loadTabData(tabName);
        });
    });

    
    loadTabData('permissions');

    
    setupModals();
    
    
    setupForms();
});

function loadTabData(tabName) {
    switch(tabName) {
        case 'permissions':
            loadPermissions();
            break;
        case 'ranks':
            loadRanks();
            break;
        case 'maintenance':
            loadMaintenance();
            break;
    }
}

function loadPermissions() {
    fetch('/api/admin/permissions')
        .then(response => response.json())
        .then(permissions => {
            const tbody = document.getElementById('permissions-list');
            tbody.innerHTML = '';
            
            permissions.forEach(permission => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-700';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">${permission.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">${permission.description || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${new Date(permission.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        <button class="text-red-400 hover:text-red-300" onclick="deletePermission(${permission.id})">
                            Supprimer
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function loadRanks() {
    fetch('/api/admin/ranks')
        .then(response => response.json())
        .then(ranks => {
            const container = document.getElementById('ranks-list');
            container.innerHTML = '';
            
            ranks.forEach(rank => {
                const div = document.createElement('div');
                div.className = 'bg-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-600';
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full" style="background-color: ${rank.color}"></div>
                            <span class="text-white font-medium">${rank.name}</span>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-blue-400 hover:text-blue-300" onclick="editRank(${rank.id})">
                                Modifier
                            </button>
                            <button class="text-red-400 hover:text-red-300" onclick="deleteRank(${rank.id})">
                                Supprimer
                            </button>
                        </div>
                    </div>
                `;
                div.addEventListener('click', () => loadRankPermissions(rank.id));
                container.appendChild(div);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function loadRankPermissions(rankId) {
    fetch(`/api/admin/rank-permissions?rank_id=${rankId}`)
        .then(response => response.json())
        .then(permissions => {
            const container = document.getElementById('rank-permissions');
            container.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-white font-medium">Permissions du rang</h4>
                    <button class="text-blue-400 hover:text-blue-300" onclick="addPermissionToRank(${rankId})">
                        Ajouter une permission
                    </button>
                </div>
            `;
            
            permissions.forEach(permission => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between bg-gray-700 rounded-lg p-3';
                div.innerHTML = `
                    <span class="text-white">${permission.name}</span>
                    <button class="text-red-400 hover:text-red-300" onclick="removePermissionFromRank(${rankId}, ${permission.id})">
                        Retirer
                    </button>
                `;
                container.appendChild(div);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function loadMaintenance() {
    fetch('/api/admin/maintenance')
        .then(response => response.json())
        .then(pages => {
            const tbody = document.getElementById('maintenance-list');
            tbody.innerHTML = '';
            
            pages.forEach(page => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-700';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">${page.page_path}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">${page.page_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs rounded-full ${page.is_maintenance ? 'bg-red-600 text-white' : 'bg-green-600 text-white'}">
                            ${page.is_maintenance ? 'En maintenance' : 'Actif'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">${page.maintenance_message || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        <button class="text-blue-400 hover:text-blue-300" onclick="editMaintenance('${page.page_path}')">
                            Modifier
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function setupModals() {
    
    const permissionModal = document.getElementById('permission-modal');
    const addPermissionBtn = document.getElementById('add-permission-btn');
    const cancelPermissionBtn = document.getElementById('cancel-permission');
    
    addPermissionBtn.addEventListener('click', () => {
        permissionModal.classList.remove('hidden');
    });
    
    cancelPermissionBtn.addEventListener('click', () => {
        permissionModal.classList.add('hidden');
    });
    
    
    const rankModal = document.getElementById('rank-modal');
    const addRankBtn = document.getElementById('add-rank-btn');
    const cancelRankBtn = document.getElementById('cancel-rank');
    
    addRankBtn.addEventListener('click', () => {
        rankModal.classList.remove('hidden');
    });
    
    cancelRankBtn.addEventListener('click', () => {
        rankModal.classList.add('hidden');
    });
    
    
    const maintenanceModal = document.getElementById('maintenance-modal');
    const addMaintenanceBtn = document.getElementById('add-maintenance-btn');
    const cancelMaintenanceBtn = document.getElementById('cancel-maintenance');
    
    addMaintenanceBtn.addEventListener('click', () => {
        maintenanceModal.classList.remove('hidden');
    });
    
    cancelMaintenanceBtn.addEventListener('click', () => {
        maintenanceModal.classList.add('hidden');
    });
    
    
    [permissionModal, rankModal, maintenanceModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
}

function setupForms() {
    
    document.getElementById('permission-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('permission-name').value;
        const description = document.getElementById('permission-description').value;
        
        fetch('/api/admin/permissions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name, description })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('permission-modal').classList.add('hidden');
                loadPermissions();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
    
    
    document.getElementById('rank-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('rank-name').value;
        const color = document.getElementById('rank-color').value;
        
        fetch('/api/admin/ranks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name, color })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('rank-modal').classList.add('hidden');
                loadRanks();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
    
    
    document.getElementById('maintenance-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const pagePath = document.getElementById('page-path').value;
        const pageName = document.getElementById('page-name').value;
        const isMaintenance = document.getElementById('is-maintenance').checked;
        const message = document.getElementById('maintenance-message').value;
        
        fetch('/api/admin/maintenance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ page_path: pagePath, page_name: pageName, is_maintenance: isMaintenance, message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('maintenance-modal').classList.add('hidden');
                loadMaintenance();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
}


function deletePermission(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')) {
        
    }
}

function editRank(id) {
    
}

function deleteRank(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rang ?')) {
        fetch(`/api/admin/ranks?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadRanks();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}

function addPermissionToRank(rankId) {
    
}

function removePermissionFromRank(rankId, permissionId) {
    if (confirm('Êtes-vous sûr de vouloir retirer cette permission ?')) {
        fetch(`/api/admin/rank-permissions?rank_id=${rankId}&permission_id=${permissionId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadRankPermissions(rankId);
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}

function editMaintenance(pagePath) {
    
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 