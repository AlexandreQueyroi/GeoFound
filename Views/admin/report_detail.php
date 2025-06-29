<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="/admin/reports" class="text-blue-400 hover:underline flex items-center">
            <iconify-icon icon="tabler:arrow-left" class="mr-2"></iconify-icon>
            Retour aux signalements
        </a>
    </div>
    
    <h1 class="text-3xl font-bold text-white mb-6">Détail du signalement #<?php echo $report['id']; ?></h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informations du signalement -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4">Informations du signalement</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-gray-400">Type:</span>
                    <span class="text-white ml-2"><?php echo ucfirst($report['type']); ?></span>
                </div>
                <div>
                    <span class="text-gray-400">Motif:</span>
                    <span class="text-white ml-2"><?php echo htmlspecialchars($report['reason']); ?></span>
                </div>
                <div>
                    <span class="text-gray-400">Détails:</span>
                    <p class="text-white mt-1"><?php echo nl2br(htmlspecialchars($report['details'])); ?></p>
                </div>
                <div>
                    <span class="text-gray-400">Signalé par:</span>
                    <span class="text-white ml-2"><?php echo htmlspecialchars($report['reporter']); ?></span>
                </div>
                <div>
                    <span class="text-gray-400">Date:</span>
                    <span class="text-white ml-2"><?php echo $report['created_at']; ?></span>
                </div>
                <div>
                    <span class="text-gray-400">Statut:</span>
                    <?php if ($report['status'] === 'pending'): ?>
                        <span class="px-2 py-1 bg-yellow-600 text-white rounded text-sm ml-2">En attente</span>
                    <?php elseif ($report['status'] === 'reviewed'): ?>
                        <span class="px-2 py-1 bg-blue-600 text-white rounded text-sm ml-2">Traité</span>
                    <?php elseif ($report['status'] === 'rejected'): ?>
                        <span class="px-2 py-1 bg-gray-600 text-white rounded text-sm ml-2">Rejeté</span>
                    <?php elseif ($report['status'] === 'sanctioned'): ?>
                        <span class="px-2 py-1 bg-red-600 text-white rounded text-sm ml-2">Sanction</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Contenu signalé -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4">Contenu signalé</h2>
            <?php if ($content): ?>
                <div class="bg-gray-700 rounded p-4">
                    <?php if ($report['type'] === 'post'): ?>
                        <div class="mb-2">
                            <span class="text-gray-400">Titre:</span>
                            <span class="text-white ml-2"><?php echo htmlspecialchars($content['title']); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400">Contenu:</span>
                            <p class="text-white mt-1"><?php echo nl2br(htmlspecialchars($content['content'])); ?></p>
                        </div>
                    <?php elseif ($report['type'] === 'message'): ?>
                        <div>
                            <span class="text-gray-400">Message:</span>
                            <p class="text-white mt-1"><?php echo nl2br(htmlspecialchars($content['message'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-400">Contenu non trouvé ou supprimé</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Actions admin -->
    <?php if ($report['status'] === 'pending'): ?>
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mt-6">
        <h2 class="text-xl font-semibold text-white mb-4">Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Rejeter le signalement -->
            <button onclick="handleReportAction(<?php echo $report['id']; ?>, 'reject')" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                Rejeter le signalement
            </button>
            
            <!-- Supprimer le contenu -->
            <button onclick="handleReportAction(<?php echo $report['id']; ?>, 'delete_content')" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                Supprimer le contenu
            </button>
            
            <!-- Sanctionner -->
            <button onclick="showSanctionModal()" 
                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition">
                Sanctionner l'utilisateur
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Historique des sanctions -->
    <?php if (!empty($sanctions)): ?>
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mt-6">
        <h2 class="text-xl font-semibold text-white mb-4">Historique des sanctions</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Raison</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Admin</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php foreach ($sanctions as $sanction): ?>
                    <tr>
                        <td class="px-4 py-2">
                            <?php if ($sanction['type'] === 'ban'): ?>
                                <span class="px-2 py-1 bg-red-600 text-white rounded text-xs">Bannissement</span>
                            <?php elseif ($sanction['type'] === 'mute'): ?>
                                <span class="px-2 py-1 bg-yellow-600 text-white rounded text-xs">Mute</span>
                            <?php elseif ($sanction['type'] === 'warning'): ?>
                                <span class="px-2 py-1 bg-orange-600 text-white rounded text-xs">Avertissement</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($sanction['reason']); ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($sanction['admin_name']); ?></td>
                        <td class="px-4 py-2 text-gray-400 text-xs"><?php echo $sanction['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de sanction -->
<div id="sanction-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-white mb-4">Sanctionner l'utilisateur</h3>
            <form id="sanction-form">
                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                <input type="hidden" name="action" value="sanction">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Type de sanction</label>
                    <select name="sanction_type" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                        <option value="warning">Avertissement</option>
                        <option value="mute">Mute temporaire</option>
                        <option value="ban">Bannissement</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Raison</label>
                    <input type="text" name="sanction_reason" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Détails (optionnel)</label>
                    <textarea name="sanction_details" rows="3" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideSanctionModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Appliquer la sanction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleReportAction(reportId, action) {
    if (action === 'delete_content' && !confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('report_id', reportId);
    formData.append('action', action);
    
    fetch('/admin/reports/action', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Action effectuée avec succès');
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
}

function showSanctionModal() {
    document.getElementById('sanction-modal').classList.remove('hidden');
}

function hideSanctionModal() {
    document.getElementById('sanction-modal').classList.add('hidden');
}

document.getElementById('sanction-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/reports/action', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sanction appliquée avec succès');
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 