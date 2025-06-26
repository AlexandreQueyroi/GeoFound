<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto mt-4 mb-8">
    <div class="flex flex-col md:flex-row h-[75vh] gap-4">

        <!-- Colonne de gauche : Liste des amis -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-secondary rounded-lg flex flex-col">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-white text-xl font-bold">Messagerie</h2>
            </div>
            <div class="flex-1 overflow-y-auto message-list">
                <ul id="friends-list" class="p-2">
                    <?php if (empty($friends)): ?>
                        <li class="p-4 text-center text-gray-400">
                            Aucun ami trouvé.
                        </li>
                    <?php else: ?>
                        <?php foreach ($friends as $friend): ?>
                            <li class="friend-item" data-friend-id="<?= htmlspecialchars($friend['id']) ?>" data-friend-online="<?= $friend['is_online'] ? 'true' : 'false' ?>">
                                <div class="flex items-center p-3 rounded-lg friend-selector hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                                    <div class="relative mr-3">
                                        <div class="bg-primary rounded-full w-12 h-12 flex items-center justify-center text-white font-bold text-xl">
                                            <?= strtoupper(substr($friend['pseudo'], 0, 1)) ?>
                                        </div>
                                        <?php if ($friend['is_online']): ?>
                                            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-500 border-2 border-secondary"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-white font-semibold"><?= htmlspecialchars($friend['pseudo']) ?></h3>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Colonne de droite : Conversation -->
        <div class="w-full md:w-2/3 lg:w-3/4 bg-secondary rounded-lg flex flex-col">
            <!-- Header de la conversation -->
            <div id="conversation-header-container" class="p-4 border-b border-gray-700" style="display: none;">
                <div class="flex items-center">
                    <div id="conversation-avatar" class="relative mr-3">
                         <div class="bg-primary rounded-full w-12 h-12 flex items-center justify-center text-white font-bold text-xl">
                            <span id="conversation-avatar-text"></span>
                        </div>
                    </div>
                    <div>
                        <h3 id="conversation-title" class="text-white font-semibold"></h3>
                        <p id="conversation-status" class="text-sm"></p>
                    </div>
                </div>
            </div>
            
            <!-- Messages -->
            <div id="conversation-container" class="flex-1 p-4 overflow-y-auto message-list">
                <div id="messages-area" class="space-y-4">
                     <div class="text-center text-gray-400 pt-16">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-white">Sélectionnez une conversation</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par choisir un ami dans la liste.</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire d'envoi -->
            <div id="message-form" class="p-4 border-t border-gray-700" style="display: none;">
                <form id="send-message-form" class="flex items-center gap-3">
                    <input type="text" id="message-input" class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-full px-4 py-2 focus:outline-none focus:border-accent" placeholder="Écrivez un message..." autocomplete="off">
                    <button type="submit" class="bg-accent text-white rounded-full p-3 hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentFriendId = null;

document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.friend-selector').forEach(item => {
        item.addEventListener('click', function() {
            const friendItem = this.closest('.friend-item');
            const friendId = friendItem.dataset.friendId;
            const friendName = this.querySelector('h3').textContent;
            const isOnline = friendItem.dataset.friendOnline === 'true';

            const statusElement = document.getElementById('conversation-status');
            if (isOnline) {
                statusElement.textContent = 'En ligne';
                statusElement.className = 'text-green-400 text-sm';
            } else {
                statusElement.textContent = 'Hors ligne';
                statusElement.className = 'text-gray-500 text-sm';
            }
            
            document.getElementById('conversation-header-container').style.display = 'block';
            document.getElementById('conversation-title').textContent = friendName;
            document.getElementById('conversation-avatar-text').textContent = friendName.charAt(0).toUpperCase();
            document.getElementById('message-form').style.display = 'block';
            
            loadConversation(friendId);
            
            document.querySelectorAll('.friend-selector').forEach(el => el.classList.remove('bg-gray-700'));
            this.classList.add('bg-gray-700');
            
            currentFriendId = friendId;
        });
    });

    function loadConversation(friendId) {
        fetch(`/message/view?friend_id=${friendId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    displayError('Erreur de chargement.');
                    return;
                }
                displayMessages(data.messages || []);
            })
            .catch(() => displayError('Erreur de connexion.'));
    }

    function displayMessages(messages) {
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = '';
        
        if (messages.length === 0) {
            messagesArea.innerHTML = `<div class="text-center text-gray-400 pt-16">...</div>`; 
            return;
        }
        
        messages.forEach(message => {
            const isOwn = message.sender_id == <?= json_encode($_SESSION['user_id'] ?? null) ?>;
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
            
            const bubbleClass = isOwn ? 'bg-accent text-white rounded-br-none' : 'bg-gray-700 text-white rounded-bl-none';
            
            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${bubbleClass}">
                    <p class="text-sm">${escapeHtml(message.content)}</p>
                    <p class="text-xs text-gray-400 text-right mt-1">${formatDate(message.posted_at)}</p>
                </div>
            `;
            messagesArea.appendChild(messageDiv);
        });
        document.getElementById('conversation-container').scrollTop = document.getElementById('conversation-container').scrollHeight;
    }

    function displayError(message) {
        document.getElementById('messages-area').innerHTML = `<div class="text-center text-red-400 pt-16">${message}</div>`;
    }

    document.getElementById('send-message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!currentFriendId) return;
        
        const messageInput = document.getElementById('message-input');
        const content = messageInput.value.trim();
        if (!content) return;

        const formData = new FormData();
        formData.append('friend_id', currentFriendId);
        formData.append('content', content);
        
        messageInput.value = '';

        fetch('/message/send', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadConversation(currentFriendId); 
                } else {
                    console.error('Erreur envoi:', data.error);
                }
            })
            .catch(error => console.error('Erreur:', error));
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?> 