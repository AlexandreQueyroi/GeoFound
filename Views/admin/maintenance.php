<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-900 rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-white mb-8">Gestion de la Maintenance</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pages en maintenance -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Pages en Maintenance</h2>
                <div id="maintenance-pages" class="space-y-3">
                    <!-- Les pages en maintenance seront chargées ici -->
                </div>
            </div>
            
            <!-- Ajouter une page -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Ajouter une Page</h2>
                <form id="add-page-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Chemin de la page</label>
                        <input type="text" id="page-path" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" placeholder="/exemple" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom de la page</label>
                        <input type="text" id="page-name" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" placeholder="Page d'exemple" required>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="is-maintenance" class="mr-2">
                            <span class="text-sm font-medium text-gray-300">En maintenance</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Message de maintenance</label>
                        <textarea id="maintenance-message" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg h-20" placeholder="Cette page est temporairement indisponible..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Ajouter la Page
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadMaintenancePages();
    
    
    document.getElementById('add-page-form').addEventListener('submit', function(e) {
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
            body: JSON.stringify({
                page_path: pagePath,
                page_name: pageName,
                is_maintenance: isMaintenance,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Page ajoutée avec succès');
                document.getElementById('add-page-form').reset();
                loadMaintenancePages();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'ajout de la page');
        });
    });
});

function loadMaintenancePages() {
    fetch('/api/admin/maintenance')
        .then(response => response.json())
        .then(pages => {
            const container = document.getElementById('maintenance-pages');
            container.innerHTML = '';
            
            if (pages.length === 0) {
                container.innerHTML = '<p class="text-gray-400">Aucune page en maintenance</p>';
                return;
            }
            
            pages.forEach(page => {
                const div = document.createElement('div');
                div.className = 'bg-gray-700 rounded-lg p-4';
                div.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-medium">${page.page_name}</h3>
                        <span class="px-2 py-1 text-xs rounded-full ${page.is_maintenance ? 'bg-red-600 text-white' : 'bg-green-600 text-white'}">
                            ${page.is_maintenance ? 'En maintenance' : 'Actif'}
                        </span>
                    </div>
                    <p class="text-gray-300 text-sm mb-2">${page.page_path}</p>
                    ${page.maintenance_message ? `<p class="text-gray-400 text-sm mb-3">${page.maintenance_message}</p>` : ''}
                    <div class="flex space-x-2">
                        <button class="text-blue-400 hover:text-blue-300 text-sm" onclick="toggleMaintenance('${page.page_path}', ${!page.is_maintenance})">
                            ${page.is_maintenance ? 'Désactiver' : 'Activer'} la maintenance
                        </button>
                        <button class="text-red-400 hover:text-red-300 text-sm" onclick="deletePage('${page.page_path}')">
                            Supprimer
                        </button>
                    </div>
                `;
                container.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('maintenance-pages').innerHTML = '<p class="text-red-400">Erreur lors du chargement des pages</p>';
        });
}

function toggleMaintenance(pagePath, isMaintenance) {
    const message = isMaintenance ? prompt('Message de maintenance (optionnel):') : null;
    
    fetch('/api/admin/maintenance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            page_path: pagePath,
            page_name: 'Page', 
            is_maintenance: isMaintenance,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadMaintenancePages();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour');
    });
}

function deletePage(pagePath) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette page ?')) {
        
        alert('Fonctionnalité de suppression à implémenter');
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 