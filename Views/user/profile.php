<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>
<?php include_once __DIR__ . '/../../Helpers/Database.php'; ?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$db = \App\Helpers\Database::getConnection();
$user_id = $_SESSION['user_id'];

// Récupérer l'ID de l'utilisateur dont on consulte le profil
$profile_user_id = $_GET['id'] ?? $user_id;
$is_own_profile = ($profile_user_id == $user_id);

// Récup info utilisateur
$stmt = $db->prepare("SELECT id, pseudo, avatar FROM users WHERE id = ?");
$stmt->execute([$profile_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: /error/404');
    exit;
}

// Nombre de posts
$stmt = $db->prepare("SELECT COUNT(*) FROM post WHERE user_id = ?");
$stmt->execute([$profile_user_id]);
$post_count = $stmt->fetchColumn() ?: 0;

// Followers
$stmt = $db->prepare("SELECT COUNT(*) FROM follow WHERE user2_id = ? AND state = 'accepted'");
$stmt->execute([$profile_user_id]);
$followers_count = $stmt->fetchColumn() ?: 0;

// Following
$stmt = $db->prepare("SELECT COUNT(*) FROM follow WHERE user1_id = ? AND state = 'accepted'");
$stmt->execute([$profile_user_id]);
$following_count = $stmt->fetchColumn() ?: 0;

// Posts de l'utilisateur
$stmt = $db->prepare("SELECT post.description, post_content.content FROM post INNER JOIN post_content ON post.content_id = post_content.id WHERE post.user_id = ? ORDER BY post.id DESC");
$stmt->execute([$profile_user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex flex-col items-center pt-12 pb-8">
    <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-44 h-44 rounded-full object-cover mb-4">
    <?php else: ?>
        <div class="w-44 h-44 rounded-full bg-gray-300 mb-4"></div>
    <?php endif; ?>
    <div class="flex items-center gap-4">
        <span class="text-white text-2xl font-light"><?= htmlspecialchars($user['pseudo']) ?></span>
        
        <?php if ($is_own_profile): ?>
            <a href="/user/edit" class="bg-gray-200 text-gray-900 px-4 py-1 rounded-lg font-semibold text-sm hover:bg-gray-300 transition">Éditer le profil</a>
        <?php else: ?>
            <!-- Bouton de signalement pour les autres utilisateurs -->
            <button onclick="openReportModal('user', <?= $user['id'] ?>)" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                <iconify-icon icon="tabler:flag" width="14" height="14"></iconify-icon>
                Signaler ce compte
            </button>
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
        <span class="text-white font-medium"><?= $post_count ?></span>
        <span class="text-gray-300">Posts</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $followers_count ?></span>
        <span class="text-gray-300">Followers</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $following_count ?></span>
        <span class="text-gray-300">Abonnements</span>
    </div>
</div>
<!-- Boutons d'export -->
<div class="flex gap-3 my-4">
    <a href="/user/export/pdf" class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-lg">Exporter PDF</a>
    <a href="/user/export/json" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-lg">Exporter JSON</a>
</div>
<div class="flex justify-center mb-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php if (is_array($posts) && count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                    <img src="data:image/jpeg;base64,<?= $post['content'] ?? $post['image'] ?? '' ?>" class="w-72 h-48 object-cover" alt="post">
                    <div class="p-2 text-white text-sm truncate"><?= htmlspecialchars($post['description'] ?? $post['desc'] ?? '') ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <span class="text-gray-400 col-span-4">Aucun post</span>
        <?php endif; ?>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 