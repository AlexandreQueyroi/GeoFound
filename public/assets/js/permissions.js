class PermissionManager {
    constructor() {
        this.currentPage = window.location.pathname;
        this.init();
    }

    init() {
        if (window.userPermissions && Array.isArray(window.userPermissions) && 
            (window.userPermissions.includes('*') || window.userPermissions.includes('admin.access'))) {
            this.addPermissionButtons();
            this.setupModal();
        }
    }

    async apiFetch(url, options = {}) {
        options.credentials = 'same-origin'; 

        const response = await fetch(url, options);

        if (!response.ok) {
            let errorJson = {};
            try {
                errorJson = await response.json();
            } catch (e) {
                
            }
            const errorMessage = errorJson.error || response.statusText || 'Erreur inconnue';
            
            if (response.status === 403) {
                throw new Error(`Accès refusé: ${errorMessage}`);
            }
            throw new Error(`Erreur ${response.status}: ${errorMessage}`);
        }
        
        const text = await response.text();
        return text ? JSON.parse(text) : {};
    }

    addPermissionButtons() {
        const existingContainer = document.getElementById('permission-buttons');
        if (existingContainer) existingContainer.remove();
        
        const permissionContainer = document.createElement('div');
        permissionContainer.id = 'permission-buttons';
        permissionContainer.className = 'fixed bottom-20 right-4 z-10 flex flex-col space-y-2';
        permissionContainer.style.zIndex = '10';
        
        const addPermissionBtn = this.createButton('Ajouter Permission Page', 'bg-blue-600 hover:bg-blue-700', () => this.showAddPermissionModal());
        const maintenanceBtn = this.createButton('Gérer Maintenance', 'bg-yellow-600 hover:bg-yellow-700', () => this.showMaintenanceModal());
        const bypassBtn = this.createButton('Bypass Maintenance', 'bg-green-600 hover:bg-green-700', () => this.toggleMaintenanceBypass());
        bypassBtn.id = 'bypass-maintenance-btn';
        
        if (localStorage.getItem('bypassMaintenance') === '1') {
            bypassBtn.classList.add('ring-4', 'ring-green-400');
            bypassBtn.innerHTML = '<span class="mr-2">Bypass Maintenance</span><iconify-icon icon="tabler:check" class="inline" width="20" height="20"></iconify-icon>';
            if (!document.getElementById('bypass-indicatif')) {
                const indic = document.createElement('div');
                indic.id = 'bypass-indicatif';
                indic.className = 'fixed bottom-32 right-4 z-[9999] bg-green-600 text-white px-4 py-2 rounded shadow-lg';
                indic.innerHTML = '<iconify-icon icon="tabler:check" class="inline mr-2" width="20" height="20"></iconify-icon>Bypass maintenance actif';
                document.body.appendChild(indic);
            }
        } else {
            const indic = document.getElementById('bypass-indicatif');
            if (indic) indic.remove();
        }
        
        permissionContainer.append(addPermissionBtn, maintenanceBtn, bypassBtn);
        document.body.appendChild(permissionContainer);
    }

    createButton(text, classes, onClick) {
        const button = document.createElement('button');
        button.className = `px-4 py-2 text-white rounded-lg transition-colors ${classes}`;
        button.innerHTML = text;
        button.addEventListener('click', onClick);
        return button;
    }

    setupModal() {
        const modal = document.getElementById('permission-maintenance-modal');
        document.getElementById('close-permission-modal')?.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
        this.setupTabs();
        this.setupForms();
        this.setupQuickActions();
    }

    setupTabs() {
        const tabButtons = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('[id$="-tab"]');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;
                tabButtons.forEach(btn => btn.classList.remove('active', 'text-blue-400', 'border-blue-400'));
                button.classList.add('active', 'text-blue-400', 'border-blue-400');
                tabContents.forEach(content => content.classList.add('hidden'));
                document.getElementById(tabName + '-tab').classList.remove('hidden');
                this.loadTabData(tabName);
            });
        });

        this.loadTabData('maintenance');
    }

    loadTabData(tabName) {
        if (tabName === 'maintenance') this.loadMaintenanceData();
        if (tabName === 'permissions') this.loadPermissionsData();
        if (tabName === 'quick-actions') this.loadQuickActionsData();
    }

    async loadMaintenanceData() {
        try {
            const pages = await this.apiFetch('/api/admin/maintenance');
            const container = document.getElementById('maintenance-list');
            container.innerHTML = '';
            
            if (!Array.isArray(pages) || pages.length === 0) {
                container.innerHTML = '<p class="text-gray-400 text-sm">Aucune page en maintenance</p>';
                return;
            }
            
            pages.forEach(page => {
                const div = this.createMaintenanceItem(page);
                container.appendChild(div);
            });
        } catch (error) {
            console.error('PermissionManager: Erreur dans loadMaintenanceData() :', error);
            document.getElementById('maintenance-list').innerHTML = `<p class="text-red-400 text-sm">${error.message}</p>`;
        }
    }

    async loadPermissionsData() {
        try {
            const permissions = await this.apiFetch('/api/admin/permissions');
            const select = document.getElementById('permission-select');
            select.innerHTML = '<option value="">Sélectionner une permission</option>';
            if (Array.isArray(permissions)) {
                permissions.forEach(p => select.add(new Option(p.name, p.id)));
            }
        } catch (error) {
            console.error('PermissionManager: Erreur dans loadPermissionsData() :', error);
            document.getElementById('permission-select').innerHTML = `<option value="">${error.message}</option>`;
        }
        this.loadPagePermissions();
    }

    async loadPagePermissions() {
        try {
            const pagePermissions = await this.apiFetch('/api/admin/page-permissions');
            const container = document.getElementById('page-permissions-list');
            container.innerHTML = '';

            if (!Array.isArray(pagePermissions) || pagePermissions.length === 0) {
                container.innerHTML = '<p class="text-gray-400 text-sm">Aucune permission de page configurée</p>';
                return;
            }
            
            pagePermissions.forEach(pp => {
                const div = this.createPermissionItem(pp);
                container.appendChild(div);
            });
        } catch (error) {
            console.error('PermissionManager: Erreur dans loadPagePermissions() :', error);
            document.getElementById('page-permissions-list').innerHTML = `<p class="text-red-400 text-sm">${error.message}</p>`;
        }
    }
    
    createMaintenanceItem(page) {
        const div = document.createElement('div');
        div.className = 'bg-gray-600 rounded p-3';
        div.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <h6 class="text-white font-medium text-sm">${page.page_name}</h6>
                <span class="px-2 py-1 text-xs rounded-full ${page.is_maintenance ? 'bg-red-600 text-white' : 'bg-green-600 text-white'}">
                    ${page.is_maintenance ? 'En maintenance' : 'Actif'}
                </span>
            </div>
            <p class="text-gray-300 text-xs mb-2">${page.page_path}</p>
            ${page.maintenance_message ? `<p class="text-gray-400 text-xs mb-2">${page.maintenance_message}</p>` : ''}
            <div class="flex space-x-2">
                <button class="text-blue-400 hover:text-blue-300 text-xs">Modifier</button>
                <button class="text-red-400 hover:text-red-300 text-xs">Supprimer</button>
            </div>
        `;
        div.querySelector('.text-blue-400').addEventListener('click', () => this.editMaintenance(page.page_path));
        div.querySelector('.text-red-400').addEventListener('click', () => this.deleteMaintenance(page.page_path));
        return div;
    }

    createPermissionItem(pp) {
        const div = document.createElement('div');
        div.className = 'bg-gray-600 rounded p-3';
        div.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <h6 class="text-white font-medium text-sm">${pp.page_path}</h6>
                <span class="text-blue-400 text-xs">${pp.permission_name}</span>
            </div>
            <div class="flex space-x-2">
                <button class="text-red-400 hover:text-red-300 text-xs">Supprimer</button>
            </div>
        `;
        div.querySelector('button').addEventListener('click', () => this.removePagePermission(pp.page_path, pp.permission_id));
        return div;
    }

    loadQuickActionsData() {
        document.getElementById('page-path').value = this.currentPage;
        document.getElementById('permission-page-path').value = this.currentPage;
    }

    setupForms() {
        const maintenanceForm = document.getElementById('maintenance-form');
        maintenanceForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveMaintenance();
        });
        document.getElementById('reset-maintenance-form')?.addEventListener('click', () => maintenanceForm.reset());
        
        const permissionForm = document.getElementById('page-permission-form');
        permissionForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.savePagePermission();
        });
        document.getElementById('reset-permission-form')?.addEventListener('click', () => permissionForm.reset());
    }

    setupQuickActions() {
        document.getElementById('quick-maintenance-all')?.addEventListener('click', () => this.quickMaintenanceAll());
        document.getElementById('quick-maintenance-none')?.addEventListener('click', () => this.quickMaintenanceNone());
        document.getElementById('quick-maintenance-current')?.addEventListener('click', () => this.quickMaintenanceCurrent());
        document.getElementById('quick-permission-public')?.addEventListener('click', () => this.quickPermissionPublic());
        document.getElementById('quick-permission-admin')?.addEventListener('click', () => this.quickPermissionAdmin());
        document.getElementById('quick-permission-logged')?.addEventListener('click', () => this.quickPermissionLogged());
    }

    async saveMaintenance() {
        try {
            await this.apiFetch('/api/admin/maintenance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    page_path: document.getElementById('page-path').value,
                    page_name: document.getElementById('page-name').value,
                    is_maintenance: document.getElementById('is-maintenance').checked,
                    message: document.getElementById('maintenance-message').value
                })
            });
            this.showNotification('Maintenance sauvegardée', 'success');
            this.loadMaintenanceData();
            document.getElementById('maintenance-form').reset();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async savePagePermission() {
        try {
            const permissionId = document.getElementById('permission-select').value;
            if (!permissionId) throw new Error('Veuillez sélectionner une permission');

            await this.apiFetch('/api/admin/page-permissions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    page_path: document.getElementById('permission-page-path').value,
                    permission_id: permissionId,
                    description: document.getElementById('permission-description').value
                })
            });
            this.showNotification('Permission ajoutée', 'success');
            this.loadPagePermissions();
            document.getElementById('page-permission-form').reset();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async editMaintenance(pagePath) {
        try {
            const page = await this.apiFetch(`/api/admin/maintenance?page_path=${encodeURIComponent(pagePath)}`);
            document.getElementById('page-path').value = page.page_path;
            document.getElementById('page-name').value = page.page_name;
            document.getElementById('is-maintenance').checked = page.is_maintenance;
            document.getElementById('maintenance-message').value = page.maintenance_message || '';
            document.querySelector('[data-tab="maintenance"]').click();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async deleteMaintenance(pagePath) {
        if (!confirm('Supprimer cette configuration ?')) return;
        try {
            await this.apiFetch(`/api/admin/maintenance?page_path=${encodeURIComponent(pagePath)}`, { method: 'DELETE' });
            this.showNotification('Maintenance supprimée', 'success');
            this.loadMaintenanceData();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async removePagePermission(pagePath, permissionId) {
        if (!confirm('Retirer cette permission ?')) return;
        try {
            await this.apiFetch(`/api/admin/page-permissions?page_path=${encodeURIComponent(pagePath)}&permission_id=${permissionId}`, { method: 'DELETE' });
            this.showNotification('Permission retirée', 'success');
            this.loadPagePermissions();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickMaintenanceAll() {
        if (!confirm('Mettre tout le site en maintenance ?')) return;
        try {
            await this.apiFetch('/api/admin/maintenance/quick-all', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_maintenance: true })
            });
            this.showNotification('Site mis en maintenance', 'success');
            this.loadMaintenanceData();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickMaintenanceNone() {
        if (!confirm('Désactiver toute la maintenance ?')) return;
        try {
            await this.apiFetch('/api/admin/maintenance/quick-all', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_maintenance: false })
            });
            this.showNotification('Maintenance désactivée', 'success');
            this.loadMaintenanceData();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickMaintenanceCurrent() {
        const message = prompt('Message de maintenance (optionnel):');
        try {
            await this.apiFetch('/api/admin/maintenance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    page_path: this.currentPage,
                    page_name: `Page actuelle (${this.currentPage})`,
                    is_maintenance: true,
                    message: message
                })
            });
            this.showNotification('Page mise en maintenance', 'success');
            this.loadMaintenanceData();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickPermissionPublic() {
        if (!confirm('Rendre la page publique (supprimer toutes les permissions) ?')) return;
        try {
            await this.apiFetch(`/api/admin/page-permissions/clear?page_path=${encodeURIComponent(this.currentPage)}`, { method: 'DELETE' });
            this.showNotification('Page rendue publique', 'success');
            this.loadPagePermissions();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickPermissionAdmin() {
        try {
            await this.apiFetch('/api/admin/page-permissions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ page_path: this.currentPage, permission_id: '2' }) 
            });
            this.showNotification('Page réservée aux admins', 'success');
            this.loadPagePermissions();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    async quickPermissionLogged() {
        try {
            await this.apiFetch('/api/admin/page-permissions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ page_path: this.currentPage, permission_id: '18' }) 
            });
            this.showNotification('Page réservée aux utilisateurs connectés', 'success');
            this.loadPagePermissions();
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    }

    showAddPermissionModal() {
        const modal = document.getElementById('permission-maintenance-modal');
        modal.classList.remove('hidden');
        document.querySelector('[data-tab="permissions"]').click();
    }

    showMaintenanceModal() {
        const modal = document.getElementById('permission-maintenance-modal');
        modal.classList.remove('hidden');
        document.querySelector('[data-tab="maintenance"]').click();
    }

    toggleMaintenanceBypass() {
        const btn = document.getElementById('bypass-maintenance-btn');
        let state = localStorage.getItem('bypassMaintenance') === '1';
        localStorage.setItem('bypassMaintenance', state ? '0' : '1');
        document.cookie = `bypass_maintenance=${state ? 0 : 1}; path=/; max-age=3600`;
        this.addPermissionButtons(); 
        this.showNotification(`Bypass maintenance ${state ? 'désactivé' : 'activé'}`, 'info');
    }

    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg ${colors[type]} text-white`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

let permissionManager;
document.addEventListener('DOMContentLoaded', () => {
    new PermissionManager();
});


function openPointsModal(userId, currentPoints, username) {
    document.getElementById('pointsModal').style.display = 'flex';
    document.getElementById('pointsUserId').value = userId;
    document.getElementById('pointsCurrentPoints').textContent = currentPoints;
    document.getElementById('pointsNewPoints').value = currentPoints;
    document.getElementById('pointsUsername').textContent = username;
    document.getElementById('pointsReason').value = '';
}


function closePointsModal() {
    document.getElementById('pointsModal').style.display = 'none';
}


async function savePoints() {
    const userId = document.getElementById('pointsUserId').value;
    const newPoints = parseInt(document.getElementById('pointsNewPoints').value);
    const reason = document.getElementById('pointsReason').value.trim();
    
    if (newPoints < 0) {
        showNotification('Les points ne peuvent pas être négatifs', 'error');
        return;
    }
    
    if (reason === '') {
        showNotification('Veuillez indiquer une raison pour la modification', 'error');
        return;
    }
    
    try {
        const response = await fetch(`/admin/users/${userId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                points: newPoints,
                reason: reason
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification('Points mis à jour avec succès', 'success');
            closePointsModal();
            
            loadUsers();
        } else {
            showNotification(result.error || 'Erreur lors de la mise à jour des points', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    }
}


async function showPointsHistory(userId, username) {
    try {
        const response = await fetch(`/admin/users/${userId}/points-history`);
        const result = await response.json();
        
        if (response.ok) {
            let historyHtml = '<div class="space-y-3">';
            
            if (result.history && result.history.length > 0) {
                result.history.forEach(entry => {
                    const change = entry.new_points - entry.old_points;
                    const changeText = change > 0 ? `+${change}` : change.toString();
                    const changeColor = change > 0 ? 'text-green-400' : change < 0 ? 'text-red-400' : 'text-gray-400';
                    
                    historyHtml += `
                        <div class="bg-gray-700 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm text-gray-300">${entry.created_at}</span>
                                <span class="text-sm font-semibold ${changeColor}">${changeText}</span>
                            </div>
                            <div class="text-sm text-gray-400">
                                <div>Ancien: ${entry.old_points} → Nouveau: ${entry.new_points}</div>
                                <div class="mt-1">Raison: ${entry.reason || 'Aucune raison spécifiée'}</div>
                                <div class="mt-1 text-xs">Par: ${entry.admin_name || 'Admin'}</div>
                            </div>
                        </div>
                    `;
                });
            } else {
                historyHtml += '<p class="text-gray-400 text-center py-4">Aucun historique disponible</p>';
            }
            
            historyHtml += '</div>';
            
            
            showModal(`Historique des points - ${username}`, historyHtml);
        } else {
            showNotification(result.error || 'Erreur lors du chargement de l\'historique', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    }
} 