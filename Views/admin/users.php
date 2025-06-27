<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- En-tête de la page -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Gestion des utilisateurs</h1>
        <p class="text-gray-400">Gérez les comptes utilisateurs, les rangs et les permissions</p>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Rechercher</label>
                <input type="text" id="search-user" placeholder="Nom d'utilisateur..." class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Rang</label>
                <select id="filter-rank" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
                    <option value="">Tous les rangs</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Statut</label>
                <select id="filter-status" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="banned">Banni</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadUsers()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <iconify-icon icon="tabler:search" class="mr-2" width="16" height="16"></iconify-icon>
                    Rechercher
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Utilisateur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Rang
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Dernière connexion
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="users-list" class="bg-gray-800 divide-y divide-gray-700">
                    <!-- Les utilisateurs seront chargés ici -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-gray-400 text-sm">
            Affichage de <span id="showing-start">1</span> à <span id="showing-end">10</span> sur <span id="total-users">0</span> utilisateurs
        </div>
        <div class="flex space-x-2">
            <button id="prev-page" onclick="previousPage()" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg disabled:opacity-50">
                Précédent
            </button>
            <button id="next-page" onclick="nextPage()" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg disabled:opacity-50">
                Suivant
            </button>
        </div>
    </div>
</div>

<!-- Modal Édition Utilisateur -->
<div id="edit-user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-medium text-white mb-4">Éditer l'utilisateur</h3>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom d'utilisateur</label>
                        <input type="text" id="edit-username" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" id="edit-email" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Rang</label>
                        <select id="edit-rank" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
                            <!-- Les rangs seront chargés ici -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Statut</label>
                        <select id="edit-status" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
                            <option value="active">Actif</option>
                            <option value="banned">Banni</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Permissions individuelles</label>
                    <div id="edit-permissions" class="bg-gray-700 rounded-lg p-3 max-h-40 overflow-y-auto">
                        <!-- Les permissions seront chargées ici -->
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
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
let currentPage = 1;
let totalPages = 1;
let users = [];

document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    loadRanks();
    loadPermissions();
    setupEventListeners();
});

function setupEventListeners() {
    // Recherche en temps réel
    document.getElementById('search-user').addEventListener('input', debounce(loadUsers, 300));
    
    // Filtres
    document.getElementById('filter-rank').addEventListener('change', loadUsers);
    document.getElementById('filter-status').addEventListener('change', loadUsers);
    
    // Formulaire d'édition
    document.getElementById('edit-user-form').addEventListener('submit', saveUser);
}

async function loadUsers() {
    try {
        const search = document.getElementById('search-user').value;
        const rank = document.getElementById('filter-rank').value;
        const status = document.getElementById('filter-status').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            search: search,
            rank: rank,
            status: status
        });
        
        const response = await fetch(`/api/admin/users?${params}`);
        const data = await response.json();
        
        if (data.success) {
            users = data.users;
            totalPages = data.total_pages;
            renderUsers();
            updatePagination();
        } else {
            showNotification('Erreur lors du chargement des utilisateurs', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement des utilisateurs', 'error');
    }
}

function renderUsers() {
    const tbody = document.getElementById('users-list');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-700';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <img class="h-10 w-10 rounded-full" src="/assets/img/avatars/default-avatar.png" alt="">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-white">${user.username}</div>
                        <div class="text-sm text-gray-400">${user.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${user.rank_color}; color: white;">
                    ${user.rank_name}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                    user.status === 'active' ? 'bg-green-100 text-green-800' :
                    user.status === 'banned' ? 'bg-red-100 text-red-800' :
                    'bg-gray-100 text-gray-800'
                }">
                    ${user.status === 'active' ? 'Actif' : user.status === 'banned' ? 'Banni' : 'Inactif'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                ${user.connected ? new Date(user.connected).toLocaleString() : 'Jamais connecté'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                <button onclick="viewUserPermissions(${user.id})" class="text-blue-400 hover:text-blue-300">
                    Voir (${user.permissions_count})
                </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editUser(${user.id})" class="text-blue-400 hover:text-blue-300 mr-3">
                    Éditer
                </button>
                <button onclick="toggleUserStatus(${user.id})" class="text-yellow-400 hover:text-yellow-300 mr-3">
                    ${user.status === 'banned' ? 'Débannir' : 'Bannir'}
                </button>
                <button onclick="deleteUser(${user.id})" class="text-red-400 hover:text-red-300">
                    Supprimer
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updatePagination() {
    const start = (currentPage - 1) * 10 + 1;
    const end = Math.min(currentPage * 10, users.length);
    
    document.getElementById('showing-start').textContent = start;
    document.getElementById('showing-end').textContent = end;
    document.getElementById('total-users').textContent = users.length;
    
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages;
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        loadUsers();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        loadUsers();
    }
}

async function loadRanks() {
    try {
        const response = await fetch('/api/admin/ranks');
        const ranks = await response.json();
        
        const filterSelect = document.getElementById('filter-rank');
        const editSelect = document.getElementById('edit-rank');
        
        ranks.forEach(rank => {
            filterSelect.innerHTML += `<option value="${rank.id}">${rank.name}</option>`;
            editSelect.innerHTML += `<option value="${rank.id}">${rank.name}</option>`;
        });
    } catch (error) {
        console.error('Erreur lors du chargement des rangs:', error);
    }
}

async function loadPermissions() {
    try {
        const response = await fetch('/api/admin/permissions');
        const permissions = await response.json();
        
        const container = document.getElementById('edit-permissions');
        permissions.forEach(permission => {
            const div = document.createElement('div');
            div.className = 'flex items-center mb-2';
            div.innerHTML = `
                <input type="checkbox" id="perm-${permission.id}" value="${permission.id}" class="mr-2">
                <label for="perm-${permission.id}" class="text-sm text-gray-300">${permission.name}</label>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Erreur lors du chargement des permissions:', error);
    }
}

async function editUser(userId) {
    try {
        const response = await fetch(`/api/admin/users/${userId}`);
        const user = await response.json();
        
        document.getElementById('edit-user-id').value = user.id;
        document.getElementById('edit-username').value = user.username;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-rank').value = user.rank_id;
        document.getElementById('edit-status').value = user.status;
        
        // Cocher les permissions de l'utilisateur
        const checkboxes = document.querySelectorAll('#edit-permissions input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = user.permissions.some(p => p.id == checkbox.value);
        });
        
        document.getElementById('edit-user-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement de l\'utilisateur', 'error');
    }
}

function closeEditModal() {
    document.getElementById('edit-user-modal').classList.add('hidden');
}

async function saveUser(e) {
    e.preventDefault();
    
    try {
        const userId = document.getElementById('edit-user-id').value;
        const formData = {
            username: document.getElementById('edit-username').value,
            email: document.getElementById('edit-email').value,
            rank_id: document.getElementById('edit-rank').value,
            status: document.getElementById('edit-status').value,
            permissions: Array.from(document.querySelectorAll('#edit-permissions input[type="checkbox"]:checked'))
                .map(cb => cb.value)
        };
        
        const response = await fetch(`/api/admin/users/${userId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        if (data.success) {
            showNotification('Utilisateur mis à jour avec succès', 'success');
            closeEditModal();
            loadUsers();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la sauvegarde', 'error');
    }
}

async function toggleUserStatus(userId) {
    if (!confirm('Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/admin/users/${userId}/toggle-status`, {
            method: 'POST'
        });
        
        const data = await response.json();
        if (data.success) {
            showNotification('Statut de l\'utilisateur modifié', 'success');
            loadUsers();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la modification du statut', 'error');
    }
}

async function deleteUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/admin/users/${userId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        if (data.success) {
            showNotification('Utilisateur supprimé avec succès', 'success');
            loadUsers();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression', 'error');
    }
}

async function viewUserPermissions(userId) {
    try {
        const response = await fetch(`/api/admin/user-permissions?user_id=${userId}`);
        const permissions = await response.json();
        
        let permissionsList = permissions.map(p => p.name).join(', ');
        if (!permissionsList) {
            permissionsList = 'Aucune permission individuelle';
        }
        
        alert(`Permissions de l'utilisateur:\n${permissionsList}`);
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement des permissions', 'error');
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
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