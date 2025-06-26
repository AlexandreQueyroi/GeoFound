<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>
<div class="flex flex-col items-center pt-4 pb-8">
    <?php if (!empty($user['avatar'])): ?>
        <img src="data:image/jpeg;base64,<?= $user['avatar'] ?>" alt="Avatar" class="w-44 h-44 rounded-full object-cover mb-4">
    <?php else: ?>
        <div class="w-44 h-44 rounded-full bg-gray-300 mb-4"></div>
    <?php endif; ?>
    <div class="flex items-center gap-4">
        <span class="text-white text-2xl font-light"><?= htmlspecialchars($user['pseudo'] ?? 'Utilisateur inconnu') ?></span>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == ($user['id'] ?? null)): ?>
            <a href="/user/edit/<?= $user['id'] ?? '' ?>" class="bg-gray-200 text-gray-900 px-4 py-1 rounded-lg font-semibold text-sm hover:bg-gray-300 transition">Éditer le profil</a>
        <?php endif; ?>
        <span class="inline-block">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"></circle>
            </svg>
        </span>
    </div>
</div>
<div class="flex justify-center gap-12 mb-6">
    <div class="text-center">
        <span class="text-white font-medium"><?= $post_count ?? 0 ?></span>
        <span class="text-gray-300">Posts</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $followers_count ?? 0 ?></span>
        <span class="text-gray-300">Abonnés</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $following_count ?? 0 ?></span>
        <span class="text-gray-300">Abonnements</span>
    </div>
</div>
<div class="flex justify-center mb-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php if (!empty($posts) && is_array($posts) && count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <?php if (!empty($post['content'])): ?>
                    <img src="data:image/jpeg;base64,<?= $post['content'] ?>" class="w-72 h-48 object-cover rounded-md" alt="post">
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <span class="text-gray-400 col-span-4">Aucun post</span>
        <?php endif; ?>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 