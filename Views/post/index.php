<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<style>

.posts-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)) !important;
    gap: 2rem !important;
    justify-items: center !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 0 1rem !important;
}

.post-card {
    width: 100% !important;
    max-width: 450px !important;
    background: #1f2937 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
    border: 1px solid #374151 !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
}

.post-card:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.4) !important;
}

.post-card button svg {
    display: inline-block;
    vertical-align: middle;
    width: 24px;
    height: 24px;
}

/* Styles spécifiques pour les émotes au survol */
.post-image-container {
    position: relative !important;
    aspect-ratio: 1 !important;
    background-color: #374151 !important;
}

.post-image-container:hover .post-hover-overlay {
    opacity: 1 !important;
    background-color: rgba(0, 0, 0, 0.1) !important;
}

.post-hover-overlay {
    position: absolute !important;
    inset: 0 !important;
    background-color: rgba(0, 0, 0, 0) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: all 0.2s ease !important;
}

.post-hover-buttons {
    display: flex !important;
    gap: 1.5rem !important;
}

.post-hover-button {
    padding: 0.75rem !important;
    background-color: rgba(255, 255, 255, 0.9) !important;
    border-radius: 50% !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.2s ease !important;
    transform: scale(1) !important;
}

.post-hover-button:hover {
    background-color: rgba(255, 255, 255, 1) !important;
    transform: scale(1.1) !important;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr !important;
        max-width: 100% !important;
    }
    
    .post-card {
        max-width: 100% !important;
    }
}

.favorite-star {
    color: #fff;
    filter: drop-shadow(0 0 2px #0008);
    transition: color 0.2s;
}
.favorite-star.favorited {
    color: #facc15;
    filter: drop-shadow(0 0 4px #facc15);
}
</style>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header de la page -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Posts</h1>
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <iconify-icon icon="tabler:search" width="20" height="20"></iconify-icon>
                    </button>
                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <iconify-icon icon="tabler:bell" width="20" height="20"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille des posts -->
    <div class="py-8">
        <div id="posts-container" class="posts-grid"></div>
        <div id="loading" class="text-center py-4 text-gray-500 hidden">Chargement...</div>
        <div id="no-more-posts" class="text-center py-4 text-gray-500 hidden">Plus de posts à afficher.</div>
    </div>
</div>

<script>
let offset = 0;
const limit = 6;
let loading = false;
let allLoaded = false;

window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

function renderPost(post) {
    const isLiked = false; // À remplacer par la logique utilisateur si besoin
    const likeCount = post.like_count ?? 0;
    const commentCount = post.comment_count ?? 0;
    return `
    <div class="post-card">
        <div class="flex items-center p-6 border-b border-gray-100 dark:border-gray-700">
            ${post.avatar ?
                `<img src="${post.avatar}" alt="Avatar" class="w-12 h-12 rounded-full object-cover mr-4" />` :
                `<div class=\"w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg mr-4\">${post.username ? post.username.charAt(0).toUpperCase() : 'U'}</div>`
            }
            <div class="flex-1">
                <div class="font-semibold text-gray-900 dark:text-white text-lg">
                    ${post.username ? post.username : 'Utilisateur inconnu'}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    ${post.date ? new Date(post.date).toLocaleDateString() : ''}
                </div>
            </div>
            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <iconify-icon icon="tabler:dots-vertical" width="20" height="20" class="text-white"></iconify-icon>
            </button>
        </div>
        ${post.content ? `
        <div class='post-image-container'>
            <img src='data:image/jpeg;base64,${post.content}' alt='Image du post' class='w-full h-full object-cover' />
            <div class="post-hover-overlay">
                <div class="post-hover-buttons">
                    <button onclick="likePost(${post.id})" class="post-hover-button">
                        <span class="iconify" data-icon="tabler:heart" style="font-size:24px;color:#ef4444"></span>
                    </button>
                    <button onclick="toggleComments(${post.id})" class="post-hover-button">
                        <span class="iconify" data-icon="tabler:message-circle" style="font-size:24px;color:#3b82f6"></span>
                    </button>
                    <button onclick="sharePost(${post.id})" class="post-hover-button">
                        <span class="iconify" data-icon="tabler:share" style="font-size:24px;color:#22c55e"></span>
                    </button>
                    <button onclick="reportPost(${post.id})" class="post-hover-button">
                        <span class="iconify" data-icon="tabler:flag" style="font-size:24px;color:#ef4444"></span>
                    </button>
                </div>
            </div>
        </div>
        ` : ''}
        <div class="p-6">
            <div class="flex items-center space-x-6 mb-4">
                <button onclick="likePost(${post.id})" class="flex items-center space-x-2 text-white hover:text-red-500 transition-colors duration-200 transform hover:scale-105">
                    <span class="iconify" data-icon="tabler:heart" style="font-size:24px;color:#ef4444"></span>
                    <span class="text-lg font-medium like-count-${post.id}">${likeCount}</span>
                </button>
                <button onclick="toggleComments(${post.id})" class="flex items-center space-x-2 text-white hover:text-blue-400 transition-colors duration-200 transform hover:scale-105">
                    <span class="iconify" data-icon="tabler:message-circle" style="font-size:24px;color:#3b82f6"></span>
                    <span class="text-lg font-medium comment-count-${post.id}">${commentCount}</span>
                </button>
                <button onclick="sharePost(${post.id})" class="flex items-center space-x-2 text-white hover:text-green-400 transition-colors duration-200 transform hover:scale-105">
                    <span class="iconify" data-icon="tabler:share" style="font-size:24px;color:#22c55e"></span>
                    <span class="text-lg font-medium">Partager</span>
                </button>
                <button onclick="reportPost(${post.id})" class="flex items-center space-x-2 text-white hover:text-red-400 transition-colors duration-200 transform hover:scale-105">
                    <span class="iconify" data-icon="tabler:flag" style="font-size:24px;color:#ef4444"></span>
                    <span class="text-lg font-medium">Signaler</span>
                </button>
                <div class="flex-1"></div>
                <button onclick="favoritePost(${post.id})" id="fav-btn-${post.id}" class="text-white hover:text-yellow-400 transition-colors duration-200 transform hover:scale-105">
                    <span class="iconify favorite-star" id="fav-star-${post.id}" data-icon="tabler:star" style="font-size:24px;"></span>
                </button>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white text-xl mb-2">${post.name ? post.name : 'Sans titre'}</h3>
            <p class="text-gray-700 dark:text-gray-300 text-base leading-relaxed">${post.description ? post.description : ''}</p>
            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-2">
                ${(post.latitude && post.longitude) ? `<div class='flex items-center space-x-1'><iconify-icon icon="tabler:map-pin" width="16" height="16" class="text-white"></iconify-icon><span>${Number(post.latitude).toFixed(4)}, ${Number(post.longitude).toFixed(4)}</span></div>` : ''}
                <span>•</span>
                <span class="cursor-pointer hover:text-blue-500 transition-colors">Voir plus</span>
            </div>
            <div id="comments-${post.id}" class="hidden mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Commentaires</h4>
                    <div id="comments-list-${post.id}" class="space-y-3"></div>
                </div>
                ${window.isLoggedIn ? `
                <form id="comment-form-${post.id}" class="flex space-x-2" onsubmit="event.preventDefault(); submitComment(${post.id});">
                    <input type="text" id="comment-input-${post.id}" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">Envoyer</button>
                </form>
                ` : `<p class="text-gray-500 dark:text-gray-400 text-sm">Connectez-vous pour commenter</p>`}
            </div>
        </div>
    </div>
    `;
}

function loadPosts() {
    if (loading || allLoaded) return;
    loading = true;
    document.getElementById('loading').classList.remove('hidden');
    fetch(`/api/posts?offset=${offset}&limit=${limit}`)
        .then(res => res.json())
        .then(data => {
            const posts = data.posts;
            if (posts.length === 0) {
                allLoaded = true;
                document.getElementById('no-more-posts').classList.remove('hidden');
            } else {
                const container = document.getElementById('posts-container');
                posts.forEach(post => {
                    container.insertAdjacentHTML('beforeend', renderPost(post));
                });
                offset += posts.length;
            }
        })
        .finally(() => {
            loading = false;
            document.getElementById('loading').classList.add('hidden');
        });
}

window.addEventListener('DOMContentLoaded', () => {
    loadPosts();
    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            loadPosts();
        }
    });
});

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
            alert('Erreur: ' + data.error);
            return;
        }
        // Met à jour le compteur de likes
        const likeCount = document.querySelector(`.like-count-${postId}`);
        if (likeCount) {
            let count = parseInt(likeCount.textContent) || 0;
            if (data.action === 'added') count++;
            if (data.action === 'removed') count = Math.max(0, count - 1);
            likeCount.textContent = count;
        }
        // Met à jour la couleur du bouton
        const btns = document.querySelectorAll(`button[onclick="likePost(${postId})"]`);
        btns.forEach(btn => {
            if (data.action === 'added') {
                btn.classList.add('text-red-500');
                btn.classList.remove('text-white');
            } else {
                btn.classList.remove('text-red-500');
                btn.classList.add('text-white');
            }
        });
    });
}

function commentPost(postId) {
    console.log('Comment post:', postId);
    
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
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    const icon = type === 'success' ? '✅' : 'ℹ️';
    
    toast.className = 'bg-gray-800 text-white p-4 rounded-lg shadow-lg flex items-center space-x-3 animate-fade-in-up';
    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

function openPostModal() {
    const modal = document.getElementById('post-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function toggleComments(postId) {
    const commentsDiv = document.getElementById(`comments-${postId}`);
    if (!commentsDiv) return;
    if (commentsDiv.classList.contains('hidden')) {
        // Charger les commentaires
        fetch(`/api/comments?post_id=${postId}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById(`comments-list-${postId}`);
                list.innerHTML = '';
                if (data.comments && data.comments.length > 0) {
                    data.comments.forEach(c => {
                        list.insertAdjacentHTML('beforeend', `
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                                <span class="font-semibold text-gray-900 dark:text-white mr-2">${c.username}</span>
                                <span class="text-gray-500 text-xs">${c.comment_at ? new Date(c.comment_at).toLocaleString() : ''}</span>
                                <div class="mt-1 text-gray-800 dark:text-gray-200">${c.content}</div>
                            </div>
                        `);
                    });
                } else {
                    list.innerHTML = '<div class="text-gray-400 text-sm">Aucun commentaire</div>';
                }
            });
        commentsDiv.classList.remove('hidden');
    } else {
        commentsDiv.classList.add('hidden');
    }
}

function submitComment(postId) {
    const form = document.getElementById(`comment-form-${postId}`);
    const input = document.getElementById(`comment-input-${postId}`);
    const content = input.value.trim();
    if (!content) return;
    fetch('/api/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showNotification('Erreur lors de l\'ajout du commentaire', 'error');
            return;
        }
        input.value = '';
        // Recharge dynamiquement les commentaires
        fetch(`/api/comments?post_id=${postId}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById(`comments-list-${postId}`);
                list.innerHTML = '';
                if (data.comments && data.comments.length > 0) {
                    data.comments.forEach(c => {
                        list.insertAdjacentHTML('beforeend', `
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                                <span class="font-semibold text-gray-900 dark:text-white mr-2">${c.username}</span>
                                <span class="text-gray-500 text-xs">${c.comment_at ? new Date(c.comment_at).toLocaleString() : ''}</span>
                                <div class="mt-1 text-gray-800 dark:text-gray-200">${c.content}</div>
                            </div>
                        `);
                    });
                } else {
                    list.innerHTML = '<div class="text-gray-400 text-sm">Aucun commentaire</div>';
                }
            });
        updateCommentCount(postId, 1);
        showNotification('Commentaire ajouté !', 'success');
    })
    .catch(error => {
        showNotification('Erreur lors de l\'ajout du commentaire', 'error');
    });
}

function updateCommentCount(postId, increment = 0) {
    const countElement = document.querySelector(`.comment-count-${postId}`);
    if (countElement) {
        const currentCount = parseInt(countElement.textContent) || 0;
        countElement.textContent = currentCount + increment;
    }
}

function toggleBookmark(button, postId) {
    fetch('/api/bookmark', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Erreur:', data.error);
            showNotification('Erreur lors de l\'action', 'error');
            return;
        }

        const svg = button.querySelector('svg');
        if (data.action === 'added') {
            svg.classList.add('fill-current', 'text-yellow-400');
            showNotification('Post ajouté aux favoris !', 'success');
        } else if (data.action === 'removed') {
            svg.classList.remove('fill-current', 'text-yellow-400');
            showNotification('Post retiré des favoris', 'info');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

const favorites = {};
function favoritePost(postId) {
    // Toggle l'état local
    favorites[postId] = !favorites[postId];
    const star = document.getElementById('fav-star-' + postId);
    if (favorites[postId]) {
        star.classList.add('favorited');
    } else {
        star.classList.remove('favorited');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.posts-grid > div');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.5s ease-out ${index * 0.1}s, transform 0.5s ease-out ${index * 0.1}s`;
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
});

function reportPost(postId) {
    if (!window.isLoggedIn) {
        showNotification('Vous devez être connecté pour signaler un post', 'error');
        return;
    }
    
    // Afficher le modal de signalement avec animation
    openReportModal('post', postId);
}

function submitReport() {
    const modal = document.getElementById('report-modal');
    const postId = modal.dataset.postId || modal.dataset.targetId;
    const type = modal.dataset.type || 'post';
    const reason = document.getElementById('report-reason').value;
    const details = document.getElementById('report-details').value;
    
    if (!reason) {
        showNotification('Veuillez sélectionner un motif', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('type', type);
    formData.append('target_id', postId);
    formData.append('reason', reason);
    formData.append('details', details);
    
    fetch('/api/report', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Signalement envoyé avec succès', 'success');
            hideReportModal();
        } else {
            showNotification('Erreur: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

function hideReportModal() {
    const modal = document.getElementById('report-modal');
    if (modal) {
        modal.classList.add('hidden');
        // Réinitialiser le formulaire
        document.getElementById('report-reason').value = '';
        document.getElementById('report-details').value = '';
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 