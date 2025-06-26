<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Bouton retour -->
        <div class="mb-6">
            <a href="/post" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                <iconify-icon icon="tabler:arrow-left" width="20" height="20"></iconify-icon>
                <span>Retour aux posts</span>
            </a>
        </div>

        <!-- Post individuel -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header du post -->
            <div class="flex items-center p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg mr-4">
                    <?php echo strtoupper(substr($post['username'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 dark:text-white text-lg">
                        <?php echo htmlspecialchars($post['username']); ?>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo isset($post['date']) ? date('d/m/Y H:i', strtotime($post['date'])) : ''; ?>
                    </div>
                </div>
                <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <iconify-icon icon="tabler:dots-vertical" width="20" height="20"></iconify-icon>
                </button>
            </div>

            <!-- Image du post -->
            <?php if (!empty($post['content'])): ?>
                <div class="relative bg-gray-100 dark:bg-gray-700">
                    <img src="data:image/jpeg;base64,<?php echo $post['content']; ?>" 
                         alt="Image du post" 
                         class="w-full max-h-96 object-cover" />
                </div>
            <?php endif; ?>

            <!-- Contenu du post -->
            <div class="p-6">
                <!-- Actions -->
                <div class="flex items-center space-x-6 mb-6">
                    <?php 
                    $isLiked = $userReaction && $userReaction['state'] === 'like';
                    $likeCount = $reactionCounts['like'] ?? 0;
                    ?>
                    <button onclick="likePost(<?php echo $post['id']; ?>)" class="flex items-center space-x-2 <?php echo $isLiked ? 'text-red-500' : 'text-gray-700 dark:text-gray-300'; ?> hover:text-red-500 transition-colors duration-200 transform hover:scale-105">
                        <iconify-icon icon="tabler:heart" width="24" height="24" class="<?php echo $isLiked ? 'fill-current' : ''; ?>"></iconify-icon>
                        <span class="text-lg font-medium like-count-<?php echo $post['id']; ?>"><?php echo $likeCount; ?></span>
                    </button>
                    <button onclick="toggleComments(<?php echo $post['id']; ?>)" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-blue-500 transition-colors duration-200 transform hover:scale-105">
                        <iconify-icon icon="tabler:message-circle" width="24" height="24"></iconify-icon>
                        <span class="text-lg font-medium comment-count-<?php echo $post['id']; ?>">0</span>
                    </button>
                    <button onclick="sharePost(<?php echo $post['id']; ?>)" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-green-500 transition-colors duration-200 transform hover:scale-105">
                        <iconify-icon icon="tabler:share" width="24" height="24"></iconify-icon>
                        <span class="text-lg font-medium">Partager</span>
                    </button>
                    <div class="flex-1"></div>
                    <button class="text-gray-700 dark:text-gray-300 hover:text-yellow-500 transition-colors duration-200 transform hover:scale-105">
                        <iconify-icon icon="tabler:bookmark" width="24" height="24"></iconify-icon>
                    </button>
                </div>

                <!-- Titre et description -->
                <div class="mb-6">
                    <h1 class="font-bold text-gray-900 dark:text-white text-2xl mb-3">
                        <?php echo htmlspecialchars($post['name']); ?>
                    </h1>
                    <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($post['description'])); ?>
                    </p>
                </div>

                <!-- Métadonnées -->
                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <?php if (!empty($post['latitude']) && !empty($post['longitude'])): ?>
                        <div class="flex items-center space-x-1">
                            <iconify-icon icon="tabler:map-pin" width="16" height="16"></iconify-icon>
                            <span><?php echo round($post['latitude'], 4); ?>, <?php echo round($post['longitude'], 4); ?></span>
                        </div>
                    <?php endif; ?>
                    <span>•</span>
                    <span>Post 
                </div>
            </div>
        </div>
    </div>
</div>

<script>

function likePost(postId) {
    fetch('/api/react', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&state=like`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Erreur:', data.error);
            return;
        }
        
        
        const likeButtons = document.querySelectorAll(`button[onclick="likePost(${postId})"]`);
        const likeCounts = document.querySelectorAll(`.like-count-${postId}`);
        
        likeButtons.forEach(likeButton => {
            const heartEmoji = likeButton.querySelector('span');
            
            if (data.action === 'added' || data.action === 'updated') {
                
                likeButton.classList.remove('text-gray-700', 'dark:text-gray-300');
                likeButton.classList.add('text-red-500');
                heartEmoji.textContent = '❤️';
            } else if (data.action === 'removed') {
                
                likeButton.classList.remove('text-red-500');
                likeButton.classList.add('text-gray-700', 'dark:text-gray-300');
                heartEmoji.textContent = '🤍';
            }
        });
        
        
        likeCounts.forEach(likeCount => {
            const currentCount = parseInt(likeCount.textContent) || 0;
            if (data.action === 'added' || data.action === 'updated') {
                likeCount.textContent = currentCount + 1;
            } else if (data.action === 'removed') {
                likeCount.textContent = Math.max(0, currentCount - 1);
            }
        });
        
        
        if (data.action === 'added') {
            showNotification('Post liké ! ❤️', 'success');
        } else if (data.action === 'removed') {
            showNotification('Like retiré', 'info');
        }
    })
    .catch(error => {
        console.error('Erreur lors du like:', error);
        showNotification('Erreur lors du like', 'error');
    });
}

function sharePost(postId) {
    
    const postUrl = `${window.location.origin}/post/${postId}`;
    
    
    if (navigator.share) {
        navigator.share({
            title: 'Post GeoFound',
            text: 'Regardez ce post intéressant !',
            url: postUrl
        }).then(() => {
            console.log('Post partagé avec succès');
        }).catch((error) => {
            console.log('Erreur lors du partage:', error);
            fallbackShare(postUrl);
        });
    } else {
        
        fallbackShare(postUrl);
    }
}

function fallbackShare(url) {
    
    navigator.clipboard.writeText(url).then(() => {
        
        showNotification('Lien copié dans le presse-papiers !', 'success');
    }).catch(() => {
        
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Lien copié dans le presse-papiers !', 'success');
    });
}

function showNotification(message, type = 'info') {
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    if (type === 'success') {
        notification.className += ' bg-green-500 text-white';
    } else if (type === 'error') {
        notification.className += ' bg-red-500 text-white';
    } else {
        notification.className += ' bg-blue-500 text-white';
    }
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <span class="text-xl">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 