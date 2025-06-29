<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- En-tête de la page -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-white mb-4">🏆 Centre de Récompenses</h1>
        <p class="text-xl text-gray-300 mb-6">Débloquez des récompenses en progressant dans votre aventure GeoFound</p>
        
        <!-- Statistiques de l'utilisateur -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold"><?php echo $user['level'] ?? 1; ?></div>
                    <div class="text-sm opacity-90">Niveau</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?php echo count($userRewards); ?></div>
                    <div class="text-sm opacity-90">Récompenses débloquées</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?php echo count($availableRewards); ?></div>
                    <div class="text-sm opacity-90">Disponibles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?php echo count($allRewards); ?></div>
                    <div class="text-sm opacity-90">Total</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
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

    <!-- Filtres -->
    <div class="flex flex-wrap gap-4 mb-8 justify-center">
        <button class="filter-btn active bg-blue-600 text-white px-4 py-2 rounded-lg transition" data-filter="all">
            Toutes
        </button>
        <button class="filter-btn bg-gray-600 text-white px-4 py-2 rounded-lg transition" data-filter="unlocked">
            Débloquées
        </button>
        <button class="filter-btn bg-gray-600 text-white px-4 py-2 rounded-lg transition" data-filter="available">
            Disponibles
        </button>
        <button class="filter-btn bg-gray-600 text-white px-4 py-2 rounded-lg transition" data-filter="locked">
            Verrouillées
        </button>
    </div>

    <!-- Grille des récompenses -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($rewardsWithStatus as $rewardData): ?>
            <?php 
            $reward = $rewardData['reward'];
            $userHas = $rewardData['user_has'];
            $userReward = $rewardData['user_reward'];
            $canUnlock = $rewardData['can_unlock'];
            
            // Déterminer la classe CSS selon le statut
            $cardClass = 'bg-gray-800 border-2';
            $iconClass = 'text-gray-400';
            
            if ($userHas) {
                $cardClass .= ' border-green-500';
                $iconClass = 'text-green-400';
                if ($userReward && $userReward['is_equipped']) {
                    $cardClass .= ' bg-green-900/20';
                    $iconClass = 'text-green-300';
                }
            } elseif ($canUnlock) {
                $cardClass .= ' border-blue-500 bg-blue-900/20';
                $iconClass = 'text-blue-400';
            } else {
                $cardClass .= ' border-gray-600 opacity-60';
                $iconClass = 'text-gray-500';
            }
            
            // Classe de filtrage
            $filterClass = 'reward-card';
            if ($userHas) {
                $filterClass .= ' unlocked';
            } elseif ($canUnlock) {
                $filterClass .= ' available';
            } else {
                $filterClass .= ' locked';
            }
            ?>
            
            <div class="<?php echo $cardClass; ?> rounded-lg p-6 transition-all duration-300 hover:scale-105 <?php echo $filterClass; ?>">
                <!-- Icône de la récompense -->
                <div class="text-center mb-4">
                    <div class="text-4xl mb-2 <?php echo $iconClass; ?>">
                        <?php echo $reward['icon'] ?? '🏆'; ?>
                    </div>
                    
                    <!-- Badge de rareté -->
                    <?php if ($reward['rarity']): ?>
                        <span class="inline-block px-2 py-1 text-xs rounded-full 
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
                    <?php endif; ?>
                </div>

                <!-- Nom et description -->
                <h3 class="text-xl font-bold text-white mb-2 text-center"><?php echo htmlspecialchars($reward['name']); ?></h3>
                <p class="text-gray-300 text-sm mb-4 text-center"><?php echo htmlspecialchars($reward['description']); ?></p>

                <!-- Informations -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Niveau requis:</span>
                        <span class="text-white"><?php echo $reward['required_level']; ?></span>
                    </div>
                    <?php if ($reward['points_value']): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Valeur:</span>
                            <span class="text-yellow-400"><?php echo $reward['points_value']; ?> pts</span>
                        </div>
                    <?php endif; ?>
                    <?php if ($userReward && $userReward['unlocked_at']): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Débloquée:</span>
                            <span class="text-green-400"><?php echo date('d/m/Y', strtotime($userReward['unlocked_at'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <?php if ($userHas): ?>
                        <!-- Récompense débloquée -->
                        <button 
                            onclick="toggleEquip(<?php echo $reward['id']; ?>)"
                            class="w-full py-2 px-4 rounded-lg transition-colors <?php echo ($userReward && $userReward['is_equipped']) ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-white'; ?>"
                        >
                            <?php if ($userReward && $userReward['is_equipped']): ?>
                                <iconify-icon icon="tabler:check" class="mr-1"></iconify-icon>
                                Équipée
                            <?php else: ?>
                                <iconify-icon icon="tabler:user-check" class="mr-1"></iconify-icon>
                                Équiper
                            <?php endif; ?>
                        </button>
                    <?php elseif ($canUnlock): ?>
                        <!-- Récompense disponible -->
                        <button 
                            onclick="unlockReward(<?php echo $reward['id']; ?>)"
                            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                        >
                            <iconify-icon icon="tabler:unlock" class="mr-1"></iconify-icon>
                            Débloquer
                        </button>
                    <?php else: ?>
                        <!-- Récompense verrouillée -->
                        <div class="w-full py-2 px-4 bg-gray-700 text-gray-400 rounded-lg text-center">
                            <iconify-icon icon="tabler:lock" class="mr-1"></iconify-icon>
                            Verrouillée
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Message si aucune récompense -->
    <?php if (empty($rewardsWithStatus)): ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-bold text-white mb-2">Aucune récompense disponible</h3>
            <p class="text-gray-400">Les récompenses apparaîtront ici au fur et à mesure de votre progression.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Script pour les récompenses -->
<script src="/assets/js/rewards.js?v=<?php echo time(); ?>"></script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 