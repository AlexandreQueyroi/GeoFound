<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../build/header.php');
include_once(__DIR__ . '/../api/bdd.php');

if (!isset($_SESSION['id'])) {
    header('Location: /');
    exit;
}

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT pseudo, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM post WHERE user_id = :id");
    $stmt->execute(['id' => $user_id]);
    $post_count = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $post_count = 0;
}

try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM follow WHERE user2_id = :id AND state = 'accepted'");
    $stmt->execute(['id' => $user_id]);
    $followers_count = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $followers_count = 0;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM follow WHERE user1_id = :id AND state = 'accepted'");
$stmt->execute(['id' => $user_id]);
$following_count = $stmt->fetchColumn() ?: 0;

$stmt = $conn->prepare("SELECT post.description, post_content.content FROM post INNER JOIN post_content ON post.content_id = post_content.id WHERE post.user_id = :id ORDER BY post.id DESC");
$stmt->execute(['id' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex flex-col items-center pt-12 pb-8">
    <?php if (!empty($user['avatar'])): ?>
        <img src="data:image/jpeg;base64,<?= $user['avatar'] ?>" alt="Avatar" class="w-44 h-44 rounded-full object-cover mb-4">
    <?php else: ?>
        <div class="w-44 h-44 rounded-full bg-gray-300 mb-4"></div>
    <?php endif; ?>
    <div class="flex items-center gap-4">
        <span class="text-white text-2xl font-light"><?= htmlspecialchars($user['pseudo']) ?></span>
        <button onclick="window.location.href='/me/edit.php'" class="bg-gray-200 text-gray-900 px-4 py-1 rounded-lg font-semibold text-sm hover:bg-gray-300 transition">Edit profil</button>
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
        <span class="text-gray-300">Post</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $followers_count ?></span>
        <span class="text-gray-300">Followers</span>
    </div>
    <div class="text-center">
        <span class="text-white font-medium"><?= $following_count ?></span>
        <span class="text-gray-300">Following</span>
    </div>
</div>
<div class="flex justify-center mb-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <img src="data:image/jpeg;base64,<?= $post['content'] ?>" class="w-72 h-48 object-cover rounded-md" alt="post">
            <?php endforeach; ?>
        <?php else: ?>
            <span class="text-gray-400 col-span-4">Aucun post</span>
        <?php endif; ?>
    </div>
</div>
<div class="fixed bottom-12 right-12">
    <button class="w-16 h-16 rounded-full bg-teal-900 flex items-center justify-center text-white text-4xl shadow-lg hover:bg-teal-700 transition">
        +
    </button>
</div>

<?php
include_once(__DIR__ . '/../build/footer.php');
?>
