<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestion des Grades</h1>
            <p class="text-gray-400">Créez et gérez les grades avec leurs permissions et couleurs personnalisées</p>
        </div>

        <!-- Messages de succès/erreur -->
        <?php if (isset($_SESSION['admin_success'])): ?>
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6 flex items-center justify-between">
                <span><?php echo htmlspecialchars($_SESSION['admin_success']); ?></span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <iconify-icon icon="tabler:x" width="20" height="20"></iconify-icon>
                </button>
            </div>
            <?php unset($_SESSION['admin_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6 flex items-center justify-between">
                <span><?php echo htmlspecialchars($_SESSION['admin_error']); ?></span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <iconify-icon icon="tabler:x" width="20" height="20"></iconify-icon>
                </button>
            </div>
            <?php unset($_SESSION['admin_error']); ?>
        <?php endif; ?>

        <!-- Statistiques des grades -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php foreach ($rankStats as $stat): ?>
                <div class="bg-[#1a2234] rounded-lg p-6 border-l-4" style="border-left-color: <?php echo $stat['color']; ?>">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($stat['display_name']); ?></p>
                            <p class="text-white text-2xl font-bold"><?php echo $stat['user_count']; ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: <?php echo $stat['color']; ?>20;">
                            <iconify-icon icon="tabler:crown" width="24" height="24" style="color: <?php echo $stat['color']; ?>"></iconify-icon>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Section Gestion des Grades -->
            <div class="space-y-6">
                <!-- Créer un nouveau grade -->
                <div class="bg-[#1a2234] rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Créer un nouveau grade</h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="create_rank">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nom technique</label>
                                <input type="text" name="name" required 
                                       class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="ex: vip, premium, etc.">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nom d'affichage</label>
                                <input type="text" name="display_name" required 
                                       class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="ex: VIP, Premium, etc.">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Couleur texte</label>
                                <input type="color" name="color" value="#3B82F6" 
                                       class="w-full h-10 bg-[#2a3244] border border-gray-600 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Couleur fond</label>
                                <input type="color" name="background_color" value="#1E40AF" 
                                       class="w-full h-10 bg-[#2a3244] border border-gray-600 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Priorité</label>
                                <input type="number" name="priority" value="10" min="0" max="100" 
                                       class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                            <textarea name="description" rows="3" 
                                      class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Description du grade..."></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Permissions</label>
                            <div class="bg-[#2a3244] border border-gray-600 rounded-lg p-3 max-h-40 overflow-y-auto">
                                <?php foreach ($availablePermissions as $permission): ?>
                                    <label class="flex items-center space-x-2 text-sm text-gray-300 mb-2">
                                        <input type="checkbox" name="permissions[]" value="<?php echo $permission['name']; ?>" 
                                               class="rounded border-gray-600 bg-[#1a2234] text-blue-600 focus:ring-blue-500">
                                        <span><?php echo htmlspecialchars($permission['description']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <iconify-icon icon="tabler:plus" class="inline mr-2" width="16" height="16"></iconify-icon>
                            Créer le grade
                        </button>
                    </form>
                </div>

                <!-- Liste des grades existants -->
                <div class="bg-[#1a2234] rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Grades existants</h2>
                    <div class="space-y-3">
                        <?php foreach ($ranks as $rank): ?>
                            <div class="flex items-center justify-between p-3 bg-[#2a3244] rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                         style="background-color: <?php echo $rank['background_color']; ?>; color: <?php echo $rank['color']; ?>;">
                                        <iconify-icon icon="tabler:crown" width="16" height="16"></iconify-icon>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium"><?php echo htmlspecialchars($rank['display_name']); ?></p>
                                        <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($rank['name']); ?> (Priorité: <?php echo $rank['priority']; ?>)</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editRank(<?php echo htmlspecialchars(json_encode($rank)); ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <iconify-icon icon="tabler:edit" width="14" height="14"></iconify-icon>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce grade ?')">
                                        <input type="hidden" name="action" value="delete_rank">
                                        <input type="hidden" name="rank_id" value="<?php echo $rank['id']; ?>">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            <iconify-icon icon="tabler:trash" width="14" height="14"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section Gestion des Utilisateurs -->
            <div class="bg-[#1a2234] rounded-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Gestion des utilisateurs</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php foreach ($users as $user): ?>
                        <div class="flex items-center justify-between p-3 bg-[#2a3244] rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        <?php echo strtoupper(substr($user['pseudo'], 0, 2)); ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-white font-medium"><?php echo htmlspecialchars($user['pseudo']); ?></p>
                                    <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <?php if ($user['rank_display_name']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" 
                                          style="background-color: <?php echo $user['rank_bg_color']; ?>; color: <?php echo $user['rank_color']; ?>;">
                                        <?php echo htmlspecialchars($user['rank_display_name']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-600 text-white">
                                        Aucun grade
                                    </span>
                                <?php endif; ?>
                                
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="action" value="update_user_rank">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="rank" class="bg-[#1a2234] border border-gray-600 text-white text-sm rounded px-2 py-1">
                                        <option value="">Aucun grade</option>
                                        <?php foreach ($ranks as $rank): ?>
                                            <option value="<?php echo $rank['name']; ?>" 
                                                    <?php echo $user['user_rank'] === $rank['name'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($rank['display_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-sm">
                                        <iconify-icon icon="tabler:check" width="12" height="12"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'édition de grade -->
<div id="edit-rank-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#1a2234] rounded-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-medium text-white mb-4">Modifier le grade</h3>
            <form method="POST" id="edit-rank-form" class="space-y-4">
                <input type="hidden" name="action" value="update_rank">
                <input type="hidden" name="rank_id" id="edit-rank-id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom technique</label>
                        <input type="text" name="name" id="edit-rank-name" required 
                               class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom d'affichage</label>
                        <input type="text" name="display_name" id="edit-rank-display-name" required 
                               class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Couleur texte</label>
                        <input type="color" name="color" id="edit-rank-color" 
                               class="w-full h-10 bg-[#2a3244] border border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Couleur fond</label>
                        <input type="color" name="background_color" id="edit-rank-bg-color" 
                               class="w-full h-10 bg-[#2a3244] border border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Priorité</label>
                        <input type="number" name="priority" id="edit-rank-priority" min="0" max="100" 
                               class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="edit-rank-description" rows="3" 
                              class="w-full bg-[#2a3244] border border-gray-600 text-white rounded-lg px-3 py-2"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRank(rank) {
    document.getElementById('edit-rank-id').value = rank.id;
    document.getElementById('edit-rank-name').value = rank.name;
    document.getElementById('edit-rank-display-name').value = rank.display_name;
    document.getElementById('edit-rank-color').value = rank.color;
    document.getElementById('edit-rank-bg-color').value = rank.background_color;
    document.getElementById('edit-rank-priority').value = rank.priority;
    document.getElementById('edit-rank-description').value = rank.description;
    
    document.getElementById('edit-rank-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-rank-modal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('edit-rank-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 