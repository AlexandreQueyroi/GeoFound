<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">🏆 Gestion des Récompenses</h1>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <iconify-icon icon="tabler:plus" class="mr-2"></iconify-icon>
            Nouvelle Récompense
        </button>
    </div>

    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-600 text-white p-4 rounded-lg mb-6 flex items-center">
            <iconify-icon icon="tabler:check" class="mr-2"></iconify-icon>
            <?php echo $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-600 text-white p-4 rounded-lg mb-6 flex items-center">
            <iconify-icon icon="tabler:alert-circle" class="mr-2"></iconify-icon>
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-600 rounded-lg p-6 text-white">
            <div class="text-3xl font-bold"><?php echo $totalRewards; ?></div>
            <div class="text-sm opacity-90">Total Récompenses</div>
        </div>
        <div class="bg-green-600 rounded-lg p-6 text-white">
            <div class="text-3xl font-bold"><?php echo $totalUnlocks; ?></div>
            <div class="text-sm opacity-90">Déblocages Totaux</div>
        </div>
        <div class="bg-purple-600 rounded-lg p-6 text-white">
            <div class="text-3xl font-bold"><?php echo $physicalRewards; ?></div>
            <div class="text-sm opacity-90">Récompenses Physiques</div>
        </div>
        <div class="bg-yellow-600 rounded-lg p-6 text-white">
            <div class="text-3xl font-bold"><?php echo $totalPoints; ?></div>
            <div class="text-sm opacity-90">Points Distribués</div>
        </div>
    </div>

    
    <div class="flex flex-wrap gap-4 mb-6">
        <select id="typeFilter" class="bg-gray-700 text-white px-4 py-2 rounded-lg">
            <option value="">Tous les types</option>
            <option value="badge">Badge</option>
            <option value="title">Titre</option>
            <option value="physical">Physique</option>
            <option value="achievement">Achievement</option>
        </select>
        <select id="rarityFilter" class="bg-gray-700 text-white px-4 py-2 rounded-lg">
            <option value="">Toutes les raretés</option>
            <option value="common">Common</option>
            <option value="rare">Rare</option>
            <option value="epic">Epic</option>
            <option value="legendary">Legendary</option>
        </select>
        <input type="text" id="searchFilter" placeholder="Rechercher..." class="bg-gray-700 text-white px-4 py-2 rounded-lg">
    </div>

    
    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <table class="w-full text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left">Icône</th>
                    <th class="px-6 py-3 text-left">Nom</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Rareté</th>
                    <th class="px-6 py-3 text-left">Niveau</th>
                    <th class="px-6 py-3 text-left">Points</th>
                    <th class="px-6 py-3 text-left">Déblocages</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rewards as $reward): ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                        <td class="px-6 py-4">
                            <div class="text-2xl">
                                <?php if ($reward['type'] === 'physical'): ?>
                                    <iconify-icon icon="<?php echo $reward['icon']; ?>"></iconify-icon>
                                <?php else: ?>
                                    <?php echo $reward['icon']; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold"><?php echo htmlspecialchars($reward['name']); ?></div>
                            <div class="text-sm text-gray-400"><?php echo htmlspecialchars($reward['description']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php 
                                switch($reward['type']) {
                                    case 'badge': echo 'bg-blue-500 text-white'; break;
                                    case 'title': echo 'bg-green-500 text-white'; break;
                                    case 'physical': echo 'bg-purple-500 text-white'; break;
                                    case 'achievement': echo 'bg-yellow-500 text-black'; break;
                                    default: echo 'bg-gray-500 text-white';
                                }
                                ?>">
                                <?php echo ucfirst($reward['type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php 
                                switch($reward['rarity']) {
                                    case 'common': echo 'bg-gray-500 text-white'; break;
                                    case 'rare': echo 'bg-blue-500 text-white'; break;
                                    case 'epic': echo 'bg-purple-500 text-white'; break;
                                    case 'legendary': echo 'bg-yellow-500 text-black'; break;
                                    default: echo 'bg-gray-500 text-white';
                                }
                                ?>">
                                <?php echo ucfirst($reward['rarity']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4"><?php echo $reward['required_level']; ?></td>
                        <td class="px-6 py-4"><?php echo $reward['points_value']; ?></td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div><?php echo $reward['unlock_count']; ?> déblocages</div>
                                <div class="text-gray-400">
                                    <?php 
                                    $percentage = $reward['total_users'] > 0 ? round(($reward['unlock_count'] / $reward['total_users']) * 100, 1) : 0;
                                    echo $percentage . '% des utilisateurs';
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button onclick="editReward(<?php echo $reward['id']; ?>)" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                                    <iconify-icon icon="tabler:edit" class="mr-1"></iconify-icon>
                                    Modifier
                                </button>
                                <button onclick="deleteReward(<?php echo $reward['id']; ?>)" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                                    <iconify-icon icon="tabler:trash" class="mr-1"></iconify-icon>
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="rewardModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-white">Nouvelle Récompense</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                <iconify-icon icon="tabler:x" width="24"></iconify-icon>
            </button>
        </div>
        
        <form id="rewardForm" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Nom</label>
                    <input type="text" name="name" id="rewardName" required 
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                </div>
                
                <div>
                    <label class="block text-white mb-2">Type</label>
                    <select name="type" id="rewardType" required 
                            class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                        <option value="badge">Badge</option>
                        <option value="title">Titre</option>
                        <option value="physical">Physique</option>
                        <option value="achievement">Achievement</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-white mb-2">Icône</label>
                    <input type="text" name="icon" id="rewardIcon" required 
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded"
                           placeholder="trophy, mug, star, etc.">
                </div>
                
                <div>
                    <label class="block text-white mb-2">Rareté</label>
                    <select name="rarity" id="rewardRarity" required 
                            class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                        <option value="common">Common</option>
                        <option value="rare">Rare</option>
                        <option value="epic">Epic</option>
                        <option value="legendary">Legendary</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-white mb-2">Niveau requis</label>
                    <input type="number" name="required_level" id="rewardLevel" required min="1" 
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                </div>
                
                <div>
                    <label class="block text-white mb-2">Points</label>
                    <input type="number" name="points_value" id="rewardPoints" required min="0" 
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-white mb-2">Description</label>
                    <textarea name="description" id="rewardDescription" required rows="3"
                              class="w-full bg-gray-700 text-white px-3 py-2 rounded"></textarea>
                </div>
                
                
                <div id="physicalFields" class="md:col-span-2 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white mb-2">Prix (€)</label>
                            <input type="number" name="price" id="rewardPrice" min="0" step="0.01"
                                   class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                        </div>
                        <div>
                            <label class="block text-white mb-2">Stock disponible</label>
                            <input type="number" name="stock" id="rewardStock" min="0"
                                   class="w-full bg-gray-700 text-white px-3 py-2 rounded">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="closeModal()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition">
                    Annuler
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentRewardId = null;

function openCreateModal() {
    currentRewardId = null;
    document.getElementById('modalTitle').textContent = 'Nouvelle Récompense';
    document.getElementById('rewardForm').reset();
    document.getElementById('rewardForm').action = '/admin/rewards/create';
    document.getElementById('physicalFields').classList.add('hidden');
    document.getElementById('rewardModal').classList.remove('hidden');
}

function editReward(rewardId) {
    currentRewardId = rewardId;
    document.getElementById('modalTitle').textContent = 'Modifier la Récompense';
    document.getElementById('rewardForm').action = '/admin/rewards/edit/' + rewardId;
    
    
    fetch('/api/admin/rewards/' + rewardId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reward = data.reward;
                document.getElementById('rewardName').value = reward.name;
                document.getElementById('rewardType').value = reward.type;
                document.getElementById('rewardIcon').value = reward.icon;
                document.getElementById('rewardRarity').value = reward.rarity;
                document.getElementById('rewardLevel').value = reward.required_level;
                document.getElementById('rewardPoints').value = reward.points_value;
                document.getElementById('rewardDescription').value = reward.description;
                
                
                if (reward.type === 'physical') {
                    document.getElementById('physicalFields').classList.remove('hidden');
                    if (reward.price) document.getElementById('rewardPrice').value = reward.price;
                    if (reward.stock) document.getElementById('rewardStock').value = reward.stock;
                }
                
                document.getElementById('rewardModal').classList.remove('hidden');
            }
        });
}

function closeModal() {
    document.getElementById('rewardModal').classList.add('hidden');
}

function deleteReward(rewardId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette récompense ?')) {
        fetch('/admin/rewards/delete/' + rewardId, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + data.error);
                }
            });
    }
}


document.getElementById('typeFilter').addEventListener('change', filterRewards);
document.getElementById('rarityFilter').addEventListener('change', filterRewards);
document.getElementById('searchFilter').addEventListener('input', filterRewards);

function filterRewards() {
    const typeFilter = document.getElementById('typeFilter').value;
    const rarityFilter = document.getElementById('rarityFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const type = row.children[2].textContent.trim();
        const rarity = row.children[3].textContent.trim();
        const name = row.children[1].textContent.toLowerCase();
        
        const typeMatch = !typeFilter || type.includes(typeFilter);
        const rarityMatch = !rarityFilter || rarity.includes(rarityFilter);
        const searchMatch = !searchFilter || name.includes(searchFilter);
        
        row.style.display = typeMatch && rarityMatch && searchMatch ? '' : 'none';
    });
}


document.getElementById('rewardType').addEventListener('change', function() {
    const physicalFields = document.getElementById('physicalFields');
    if (this.value === 'physical') {
        physicalFields.classList.remove('hidden');
    } else {
        physicalFields.classList.add('hidden');
    }
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 