<?php
include_once(__DIR__ . '/build/header.php');
include_once(__DIR__ . '/api/bdd.php');

$post_id = intval($_GET['id'] ?? 0);
if ($post_id === 0) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT p.*, pc.content, u.pseudo as author
        FROM post p
        JOIN post_content pc ON p.content_id = pc.id
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: index.php');
        exit;
    }

    $user_reactions = [];
    if (isset($_SESSION['id'])) {
        $stmt = $conn->prepare("SELECT state FROM reaction WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['id']]);
        $user_reaction = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user_reaction) {
            $user_reactions[$post_id] = $user_reaction['state'];
        }
    }

    $stmt = $conn->prepare("SELECT state, COUNT(*) as count FROM reaction WHERE post_id = ? GROUP BY state");
    $stmt->execute([$post_id]);
    $reactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $reaction_counts = [];
    foreach ($reactions as $reaction) {
        $reaction_counts[$reaction['state']] = $reaction['count'];
    }

    $stmt = $conn->prepare("
        SELECT c.*, u.pseudo
        FROM comment c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.comment_at DESC
    ");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center gap-4 mb-4">
            <img src="img/examplePostProfil.jpg" alt="Avatar" class="h-10 w-10 rounded-full">
            <div>
                <h1 class="text-xl font-semibold"><?php echo htmlspecialchars($post['author']); ?></h1>
                <p class="text-gray-500 text-sm"><?php echo $post['date'] ? date('d/m/Y H:i', strtotime($post['date'])) : 'Date inconnue'; ?></p>
            </div>
        </div>

        <?php if ($post['latitude'] && $post['longitude']): ?>
        <div class="text-gray-500 text-sm mb-4">
            <i class="fas fa-map-marker-alt"></i>
            <?php echo htmlspecialchars($post['latitude'] . ', ' . $post['longitude']); ?>
        </div>
        <?php endif; ?>

        <div class="mb-4">
            <p class="text-gray-800 mb-4"><?php echo htmlspecialchars($post['description']); ?></p>
            <img src="data:image/jpeg;base64,<?php echo $post['content']; ?>" alt="Image du post" class="w-full rounded-lg">
        </div>

        <div class="flex items-center gap-4 border-t border-b py-4 my-4">
            <button onclick="react(<?= $post['id'] ?>, 'like')" class="flex items-center gap-1">
                <svg class="w-6 h-6 <?= isset($user_reactions[$post['id']]) && $user_reactions[$post['id']] === 'like' ? 'text-red-500' : 'text-gray-400' ?>" fill="<?= isset($user_reactions[$post['id']]) && $user_reactions[$post['id']] === 'like' ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
                <span class="text-gray-400"><?= $reaction_counts['like'] ?? 0 ?></span>
            </button>
            <button onclick="scrollToComments()" class="flex items-center gap-1">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                </svg>
                <span class="text-gray-400"><?= count($comments) ?></span>
            </button>
            <button onclick="copyPostLink(<?= $post['id'] ?>)" class="flex items-center gap-1">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                </svg>
            </button>
        </div>

        <?php if (isset($_SESSION['id'])): ?>
        <div class="comment-form mb-8">
            <form id="commentForm" onsubmit="return submitComment(event)" class="space-y-4">
                <textarea name="content" placeholder="Votre commentaire..." required class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 transition">Commenter</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="comments-section">
            <h2 class="text-xl font-semibold mb-4">Commentaires</h2>
            <div id="commentsList" class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                <div class="comment bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-semibold"><?php echo htmlspecialchars($comment['pseudo']); ?></span>
                        <span class="text-gray-500 text-sm"><?php echo date('d/m/Y H:i', strtotime($comment['comment_at'])); ?></span>
                    </div>
                    <div class="text-gray-800">
                        <?php echo htmlspecialchars($comment['content']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function react(postId, state) {
    fetch('api/react.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&state=${state}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            location.reload();
        }
    });
}

function submitComment(event) {
    event.preventDefault();
    const form = event.target;
    const content = form.content.value;

    fetch('api/comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=<?php echo $post_id; ?>&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            const comment = data.comment;
            const commentsList = document.getElementById('commentsList');
            const commentHtml = `
                <div class="comment bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-semibold">${comment.user}</span>
                        <span class="text-gray-500 text-sm">${comment.date}</span>
                    </div>
                    <div class="text-gray-800">
                        ${comment.content}
                    </div>
                </div>
            `;
            commentsList.insertAdjacentHTML('afterbegin', commentHtml);
            form.reset();
        }
    });
    return false;
}

function scrollToComments() {
    document.querySelector('.comments-section').scrollIntoView({ behavior: 'smooth' });
}

function copyPostLink(postId) {
    const url = window.location.origin + '/post.php?id=' + postId;
    navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié !');
    }).catch(err => {
        console.error('Erreur lors de la copie du lien:', err);
    });
}
</script>

<?php
include_once(__DIR__ . '/build/footer.php');
?> 