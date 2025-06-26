<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php echo 'HEADER OK'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>
<div class="flex-grow max-w-4xl mx-auto px-4 py-8">
    <div class="text-black">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $row): ?>
                <div class="bg-white max-w-lg mx-auto p-4 rounded-lg shadow-lg mb-4" id="post">
                    <div id="postProfil" class="flex items-left p-4 gap-x-2">
                        <img src="assets/img/examplePostProfil.jpg" alt="Image" class="h-8 w-8 object-contain rounded-full aspect-square">
                        <h3><?= htmlspecialchars($row['user_name']) ?></h3>
                    </div>
                    <div>
                        <img id="postPicture" src="data:image/jpeg;base64,<?= $row['content'] ?>" alt="Image" class="w-auto h-max-60 object-contain">
                        <div id="postReaction" class="flex justify-between p-4 bg-white">
                            <div class="flex items-center gap-4">
                                <?php
                                $heart_color = ($row['user_reaction'] && $row['user_reaction']['state'] === 'like') ? 'text-red-500' : 'text-gray-400';
                                ?>
                                <button onclick="react(<?= $row['id'] ?>, 'like')" class="flex items-center gap-1">
                                    <svg class="w-6 h-6 <?= $heart_color ?>" fill="<?= ($row['user_reaction'] && $row['user_reaction']['state'] === 'like' ? 'currentColor' : 'none') ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <?php if (isset($row['reaction_counts']['like'])): ?>
                                        <span class="text-gray-400"><?= $row['reaction_counts']['like'] ?></span>
                                    <?php endif; ?>
                                </button>

                                <button onclick="showComments(<?= $row['id'] ?>)" class="flex items-center gap-1">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                                    </svg>
                                </button>

                                <button onclick="copyPostLink(<?= $row['id'] ?>)" class="flex items-center gap-1">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($row['description']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-gray-500">Aucun post trouvé</div>
        <?php endif; ?>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
function sharePost(postId) {
    const url = `${window.location.origin}/post/${postId}`;
    if (navigator.share) {
        navigator.share({
            title: 'GeoFound - Post',
            url: url
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('Lien copié dans le presse-papiers !');
        });
    }
}
</script> 