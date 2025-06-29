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
                            Points
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

<!-- Modal Modification Points -->
<div id="edit-points-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-white">Modifier les Points</h3>
                <button onclick="closePointsModal()" class="text-gray-400 hover:text-white">
                    <iconify-icon icon="tabler:x" width="24"></iconify-icon>
                </button>
            </div>
            <form id="edit-points-form">
                <input type="hidden" id="edit-points-user-id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Utilisateur</label>
                    <div id="edit-points-username" class="bg-gray-700 text-white px-3 py-2 rounded-lg font-semibold"></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Points actuels</label>
                    <div id="edit-points-current" class="bg-gray-700 text-white px-3 py-2 rounded-lg"></div>
                </div>
                <div class="mb-4">
                    <label for="edit-points-new" class="block text-sm font-medium text-gray-300 mb-2">Nouveaux points</label>
                    <input type="number" id="edit-points-new" name="points" required min="0" 
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg">
                </div>
                <div class="mb-6">
                    <label for="edit-points-reason" class="block text-sm font-medium text-gray-300 mb-2">Raison (optionnel)</label>
                    <textarea id="edit-points-reason" name="reason" rows="3" 
                              class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg"
                              placeholder="Raison de la modification..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePointsModal()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay de chargement -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
            <span class="ml-3 text-white">Chargement...</span>
        </div>
    </div>
</div>

<!-- Container pour les notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
console.log('=== SCRIPT ADMIN USERS CHARGÉ ===');
console.log('Session user_id:', <?php echo $_SESSION['user_id'] ?? 'null'; ?>);

let currentPage = 1;
let totalPages = 1;
let users = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOMContentLoaded TRIGGERED ===');
    console.log('ID de session actuel:', <?php echo $_SESSION['user_id'] ?? 'null'; ?>);
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
    
    // Formulaire de modification des points
    document.getElementById('edit-points-form').addEventListener('submit', function(e) {
        e.preventDefault();
        savePoints();
    });
    
    // Fermer la modal en cliquant à l'extérieur
    document.getElementById('edit-user-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    
    // Fermer la modal de points en cliquant à l'extérieur
    document.getElementById('edit-points-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePointsModal();
        }
    });
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
        
        console.log('Chargement des utilisateurs avec params:', params.toString());
        
        const response = await fetch(`/api/admin/users?${params}`);
        console.log('Réponse API status:', response.status);
        
        const data = await response.json();
        console.log('Données API reçues:', data);
        
        if (data.success) {
            users = data.users;
            totalPages = data.total_pages;
            console.log('Utilisateurs chargés:', users.length);
            renderUsers();
            updatePagination();
        } else {
            console.error('Erreur API:', data.error);
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
    
    console.log('Rendu de', users.length, 'utilisateurs');
    console.log('ID de session actuel:', <?php echo $_SESSION['user_id'] ?? 'null'; ?>);
    
    users.forEach((user, index) => {
        console.log(`Utilisateur ${index + 1}:`, user);
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-700';
        
        // Préparer l'affichage du grade
        let rankDisplay = '';
        if (user.rank_display_name) {
            rankDisplay = `
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                      style="background-color: ${user.rank_bg_color || '#1E40AF'}; color: ${user.rank_color || '#FFFFFF'};">
                    ${user.rank_display_name}
                </span>
            `;
        } else {
            rankDisplay = `
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-600 text-white">
                    Aucun grade
                </span>
            `;
        }
        
        const currentUserId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
        const isOwnAccount = false; // Permettre l'auto-édition
        
        console.log(`User ID ${user.id} vs Current User ID ${currentUserId}:`, isOwnAccount ? 'PROPRE COMPTE' : 'AUTRE UTILISATEUR');
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                ${user.pseudo.substring(0, 2).toUpperCase()}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-white">${user.pseudo}</div>
                        <div class="text-sm text-gray-400">${user.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${rankDisplay}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                    <span class="text-lg font-bold text-yellow-400">${user.point || 0}</span>
                    <button onclick="openPointsModal(${user.id}, '${user.pseudo}', ${user.point || 0})" 
                            class="text-green-400 hover:text-green-300 transition-colors px-2 py-1 border border-green-400 rounded text-xs"
                            title="Modifier les points">
                        <iconify-icon icon="tabler:edit" class="mr-1"></iconify-icon>
                        Modifier
                    </button>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                        user.status == 0 ? 'bg-green-600 text-white' :
                        user.status == 1 ? 'bg-red-600 text-white' :
                        user.status == 2 ? 'bg-yellow-600 text-white' :
                        'bg-gray-600 text-white'
                    }">
                        ${user.status == 0 ? 'Actif' : 
                          user.status == 1 ? 'Banni' : 
                          user.status == 2 ? 'Inactif' : 'Inconnu'}
                    </span>
                    ${user.email_verified ? 
                        '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white">✓ Email</span>' : 
                        '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-600 text-white">Email non vérifié</span>'
                    }
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                ${user.connected ? new Date(user.connected).toLocaleString('fr-FR') : 'Jamais connecté'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                <button onclick="viewUserPermissions(${user.id})" class="text-blue-400 hover:text-blue-300">
                    Voir (${user.permissions_count || 0})
                </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                <div class="flex space-x-2">
                    <button onclick="editUser(${user.id})" 
                            class="text-blue-400 hover:text-blue-300 transition-colors px-2 py-1 border border-blue-400 rounded"
                            title="Modifier">
                        ÉDITER
                    </button>
                    <button onclick="toggleUserStatus(${user.id})" 
                            class="text-yellow-400 hover:text-yellow-300 transition-colors px-2 py-1 border border-yellow-400 rounded"
                            title="${user.status == 1 ? 'Réactiver' : 'Bannir'}">
                        BANNIR
                    </button>
                    <button onclick="viewUserPermissions(${user.id})" 
                            class="text-green-400 hover:text-green-300 transition-colors px-2 py-1 border border-green-400 rounded"
                            title="Voir les permissions">
                        PERMISSIONS
                    </button>
                    <button onclick="deleteUser(${user.id})" 
                            class="text-red-400 hover:text-red-300 transition-colors px-2 py-1 border border-red-400 rounded"
                            title="Supprimer">
                        SUPPRIMER
                    </button>
                </div>
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
        
        // Vider les selects d'abord
        filterSelect.innerHTML = '<option value="">Tous les grades</option>';
        editSelect.innerHTML = '<option value="">Aucun grade</option>';
        
        ranks.forEach(rank => {
            const optionText = `${rank.display_name} (${rank.name})`;
            filterSelect.innerHTML += `<option value="${rank.name}">${optionText}</option>`;
            editSelect.innerHTML += `<option value="${rank.name}">${optionText}</option>`;
        });
    } catch (error) {
        console.error('Erreur lors du chargement des grades:', error);
    }
}

async function loadPermissions() {
    try {
        const response = await fetch('/api/admin/permissions');
        const permissions = await response.json();
        
        const container = document.getElementById('edit-permissions');
        container.innerHTML = ''; // Vider le conteneur d'abord
        
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
        console.log('Chargement de l\'utilisateur:', userId);
        
        const response = await fetch(`/api/admin/users/${userId}`);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const user = await response.json();
        console.log('Données utilisateur reçues:', user);
        
        if (user.error) {
            throw new Error(user.error);
        }
        
        document.getElementById('edit-user-id').value = user.id;
        document.getElementById('edit-username').value = user.pseudo;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-rank').value = user.rank_name || '';
        
        console.log('Grade sélectionné:', user.rank_name);
        
        // Gérer les 3 statuts
        let statusValue = 'active';
        if (user.status == 1) {
            statusValue = 'banned';
        } else if (user.status == 2) {
            statusValue = 'inactive';
        }
        document.getElementById('edit-status').value = statusValue;
        
        // Cocher les permissions de l'utilisateur
        const checkboxes = document.querySelectorAll('#edit-permissions input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = user.permissions && user.permissions.some(p => p.id == checkbox.value);
        });
        
        document.getElementById('edit-user-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement de l\'utilisateur: ' + error.message, 'error');
    }
}

function closeEditModal() {
    document.getElementById('edit-user-modal').classList.add('hidden');
    // Réinitialiser le formulaire
    document.getElementById('edit-user-form').reset();
    // Décocher toutes les permissions
    const checkboxes = document.querySelectorAll('#edit-permissions input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}

function showLoading(show = true) {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (show) {
        loadingOverlay.classList.remove('hidden');
    } else {
        loadingOverlay.classList.add('hidden');
    }
}

async function saveUser(e) {
    e.preventDefault();
    
    try {
        showLoading(true);
        
        const userId = document.getElementById('edit-user-id').value;
        const currentUserId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
        
        // Empêcher la modification de son propre compte
        if (userId == currentUserId) {
            showNotification('Vous ne pouvez pas modifier votre propre compte', 'error');
            return;
        }
        
        const pseudo = document.getElementById('edit-username').value.trim();
        const email = document.getElementById('edit-email').value.trim();
        const rankName = document.getElementById('edit-rank').value;
        const status = document.getElementById('edit-status').value;
        const permissions = Array.from(document.querySelectorAll('#edit-permissions input[type="checkbox"]:checked'))
            .map(cb => cb.value);
        
        // Validations côté client
        if (!pseudo) {
            showNotification('Le nom d\'utilisateur est requis', 'error');
            return;
        }
        
        if (!email) {
            showNotification('L\'email est requis', 'error');
            return;
        }
        
        if (!email.includes('@')) {
            showNotification('L\'email n\'est pas valide', 'error');
            return;
        }
        
        const formData = {
            username: pseudo,
            email: email,
            rank_name: rankName,
            status: status,
            permissions: permissions
        };
        
        const response = await fetch(`/api/admin/users/${userId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            showNotification('Utilisateur mis à jour avec succès', 'success');
            closeEditModal();
            loadUsers();
        } else {
            showNotification(data.error || 'Erreur lors de la sauvegarde', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la sauvegarde: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

async function toggleUserStatus(userId) {
    if (!confirm('Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?')) {
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch(`/api/admin/users/${userId}/toggle-status`, {
            method: 'POST'
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            showNotification('Statut de l\'utilisateur modifié', 'success');
            loadUsers();
        } else {
            showNotification(data.error || 'Erreur lors de la modification du statut', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la modification du statut: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

async function deleteUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch(`/api/admin/users/${userId}`, {
            method: 'DELETE'
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            showNotification('Utilisateur supprimé avec succès', 'success');
            loadUsers();
        } else {
            showNotification(data.error || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

async function viewUserPermissions(userId) {
    try {
        const response = await fetch(`/api/admin/user-permissions?user_id=${userId}`);
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const permissions = await response.json();
        
        let permissionsList = permissions.map(p => p.name).join(', ');
        if (!permissionsList) {
            permissionsList = 'Aucune permission individuelle';
        }
        
        // Utiliser une modale plus moderne au lieu d'alert
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium text-white mb-4">Permissions de l'utilisateur</h3>
                <p class="text-gray-300 mb-4">${permissionsList}</p>
                <div class="flex justify-end">
                    <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Fermer
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Fermer la modale en cliquant à l'extérieur
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement des permissions: ' + error.message, 'error');
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

// === FONCTIONS POUR LA GESTION DES POINTS ===

function openPointsModal(userId, username, currentPoints) {
    document.getElementById('edit-points-user-id').value = userId;
    document.getElementById('edit-points-username').textContent = username;
    document.getElementById('edit-points-current').textContent = currentPoints.toLocaleString();
    document.getElementById('edit-points-new').value = currentPoints;
    document.getElementById('edit-points-reason').value = '';
    document.getElementById('edit-points-modal').classList.remove('hidden');
}

function closePointsModal() {
    document.getElementById('edit-points-modal').classList.add('hidden');
}

async function savePoints() {
    try {
        showLoading(true);
        
        const userId = document.getElementById('edit-points-user-id').value;
        const newPoints = parseInt(document.getElementById('edit-points-new').value);
        const reason = document.getElementById('edit-points-reason').value.trim();
        
        if (newPoints < 0) {
            showNotification('Les points ne peuvent pas être négatifs', 'error');
            return;
        }
        
        if (reason === '') {
            showNotification('Veuillez indiquer une raison pour la modification', 'error');
            return;
        }
        
        const response = await fetch(`/api/admin/users/${userId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                points: newPoints,
                reason: reason
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            showNotification('Points mis à jour avec succès', 'success');
            closePointsModal();
            loadUsers(); // Recharger la liste des utilisateurs
        } else {
            showNotification(data.error || 'Erreur lors de la mise à jour des points', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour des points: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 