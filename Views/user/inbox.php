<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>
<style>
.selected {
    background-color: #2563eb !important; /* Couleur de sélection (bleu Tailwind) */
}
</style>
<div class="container mx-auto px-2 py-2 md:px-4 md:py-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 h-[calc(100vh-12rem)]">
        <div class="bg-secondary rounded-lg p-4 flex flex-col h-full">
            <h2 class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3">Amis</h2>
            <div class="flex-1 overflow-y-auto min-h-0">
                <ul id="friend-list" class="space-y-2">
                    <?php foreach ($friends ?? [] as $f): ?>
                        <li data-id="<?= $f['id'] ?>" data-pseudo="<?= htmlspecialchars($f['pseudo']) ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 cursor-pointer<?php if (isset($selected_friend) && $selected_friend == $f['id']) echo ' selected'; ?>">
                            <?php if (!empty($f['avatar_id'])): ?>
                                <img src="<?= \App\Helpers\AvatarHelper::getAvatarUrl($f['avatar_id']) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover mr-2">
                            <?php else: ?>
                                <div class="bg-primary rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-lg mr-2">
                                    <?= \App\Helpers\AvatarHelper::getInitials($f['pseudo']) ?>
                                </div>
                            <?php endif; ?>
                            <span class="text-white"><?= htmlspecialchars($f['pseudo']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="mt-2 md:mt-3 space-y-2">
                <button id="add-friend-btn" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 md:px-4 md:py-2">Ajouter un ami</button>
                <button id="friend-requests-btn" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-2 md:px-4 md:py-2">Demandes d'amis</button>
            </div>
        </div>
        <div class="bg-secondary rounded-lg p-4 flex flex-col h-full md:col-span-3">
            <div id="conversation-header" class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3"></div>
            <div id="messages" class="flex-1 overflow-y-auto space-y-3 mb-2 md:mb-3 pr-2 custom-scrollbar" style="max-height: 60vh; min-height: 200px;">
                <div id="messages-list">
                    <div class="flex justify-center items-center h-full text-gray-400 text-center">Cliquez sur un utilisateur pour commencer à discuter</div>
                </div>
            </div>
            <form id="send-message-form" class="mt-auto flex gap-2 hidden">
                <input type="text" id="message-input" class="flex-1 bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2" placeholder="Votre message..." autocomplete="off">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Envoyer</button>
            </form>
        </div>
    </div>
</div>
<div id="add-friend-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-[
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-xl font-semibold text-white">Ajouter un ami</h3>
                <button type="button" id="close-add-friend" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http:
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <input type="text" id="add-friend-pseudo" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mb-4" placeholder="Pseudo de l'ami">
                <button id="send-friend-request" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full">Envoyer la demande</button>
                <div id="add-friend-feedback" class="mt-4 text-white"></div>
            </div>
        </div>
    </div>
</div>
<div id="friend-requests-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-[
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-xl font-semibold text-white">Demandes d'amis</h3>
                <button type="button" id="close-friend-requests" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http:
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-white mb-2">Demandes reçues</h4>
                    <ul id="received-requests" class="space-y-2"></ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-2">Demandes envoyées</h4>
                    <ul id="sent-requests" class="space-y-2"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.selectedFriendId = null;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script src="/assets/js/messagerie.js"></script>
<script>
function renderMessages(messages, userId) {
    const messagesList = document.getElementById('messages-list');
    messagesList.innerHTML = '';
    if (!messages || messages.length === 0) {
        messagesList.innerHTML = '<div class="flex justify-center items-center h-full text-gray-400 text-center">Ceci est le début de votre conversation</div>';
        return;
    }
    messages.forEach(m => {
        const isOwn = m.sent || m.sender_id == userId;
        const wrapper = document.createElement('div');
        wrapper.className = 'flex' + (isOwn ? ' justify-end' : ' justify-start') + ' mb-3';
        const bubble = document.createElement('div');
        bubble.className = 'max-w-[70%] px-4 py-2 rounded-2xl shadow ' + (isOwn ? 'bg-blue-600 text-white rounded-br-none ml-8' : 'bg-gray-700 text-white rounded-bl-none mr-8');
        const content = document.createElement('div');
        content.className = 'text-sm';
        content.textContent = m.content;
        bubble.appendChild(content);
        const time = document.createElement('div');
        time.className = 'text-xs text-gray-300 text-right mt-1';
        let date = m.posted_at || m.time;
        if (date) {
            const d = new Date(date);
            time.textContent = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
        bubble.appendChild(time);
        wrapper.appendChild(bubble);
        messagesList.appendChild(wrapper);
    });
    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
}

function loadConversation(friendId, scrollToBottom = true) {
    fetch('/message/conversation?friend_id=' + friendId)
        .then(r => r.json())
        .then(data => {
            // Accepte les deux formats de réponse
            if (Array.isArray(data)) {
                renderMessages(data, window.currentUserId);
                if (scrollToBottom) {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            } else if (data && data.messages) {
                renderMessages(data.messages, window.currentUserId);
                if (scrollToBottom) {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        });
}

function setConversationHeader(pseudo) {
    document.getElementById('conversation-header').textContent = pseudo ? 'Conversation avec ' + pseudo : '';
}

document.addEventListener('DOMContentLoaded', function() {
    window.currentUserId = <?= json_encode($_SESSION['user_id'] ?? null) ?>;
    // Initialisation : tout masquer
    setConversationHeader('');
    document.getElementById('send-message-form').classList.add('hidden');
    document.getElementById('messages-list').innerHTML = '<div class="flex justify-center items-center h-full text-gray-400 text-center">Cliquez sur un utilisateur pour commencer à discuter</div>';
    document.querySelectorAll('#friend-list li').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const pseudo = this.getAttribute('data-pseudo');
            setConversationHeader(pseudo);
            window.selectedFriendId = id;
            loadConversation(id, true);
            document.querySelectorAll('#friend-list li').forEach(li => li.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('send-message-form').classList.remove('hidden');
        });
    });
    // Envoi de message AJAX
    const form = document.getElementById('send-message-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('message-input');
            const content = input.value.trim();
            if (!content || !window.selectedFriendId) return;
            fetch('/message/send', {
                method: 'POST',
                body: new URLSearchParams({ friend_id: window.selectedFriendId, content })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadConversation(window.selectedFriendId, true);
                    input.value = '';
                }
            });
        });
    }

    // Gestion des modaux
    // Ouvrir le modal Ajouter un ami
    document.getElementById('add-friend-btn').addEventListener('click', function() {
        document.getElementById('add-friend-modal').classList.remove('hidden');
    });
    document.getElementById('close-add-friend').addEventListener('click', function() {
        document.getElementById('add-friend-modal').classList.add('hidden');
    });

    // Ouvrir le modal Demandes d'amis
    document.getElementById('friend-requests-btn').addEventListener('click', function() {
        document.getElementById('friend-requests-modal').classList.remove('hidden');
        loadFriendRequests();
    });
    document.getElementById('close-friend-requests').addEventListener('click', function() {
        document.getElementById('friend-requests-modal').classList.add('hidden');
    });

    // Envoi de la demande d'ami
    document.getElementById('send-friend-request').addEventListener('click', function() {
        const pseudo = document.getElementById('add-friend-pseudo').value.trim();
        if (!pseudo) return;
        fetch('/friend/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pseudo=' + encodeURIComponent(pseudo)
        })
        .then(r => r.text())
        .then(msg => {
            document.getElementById('add-friend-feedback').textContent = msg;
            if (msg.includes('envoyée') || msg.includes('Demande')) {
                document.getElementById('add-friend-pseudo').value = '';
                setTimeout(() => {
                    document.getElementById('add-friend-modal').classList.add('hidden');
                }, 2000);
            }
        })
        .catch(error => {
            document.getElementById('add-friend-feedback').textContent = 'Erreur lors de l\'envoi de la demande';
        });
    });

    // Charger les demandes d'amis
    function loadFriendRequests() {
        fetch('/friend/requests')
            .then(r => r.json())
            .then(data => {
                const received = data.received || [];
                const sent = data.sent || [];
                const receivedList = document.getElementById('received-requests');
                const sentList = document.getElementById('sent-requests');
                
                receivedList.innerHTML = received.length ? '' : '<li class="text-gray-400">Aucune demande reçue</li>';
                sentList.innerHTML = sent.length ? '' : '<li class="text-gray-400">Aucune demande envoyée</li>';
                
                received.forEach(req => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between bg-gray-700 rounded-lg p-2';
                    li.innerHTML = `
                        <span class="text-white">${req.pseudo}</span>
                        <div class="flex space-x-2">
                            <button class="px-2 py-1 bg-green-600 text-white rounded accept-request" data-id="${req.id}">Accepter</button>
                            <button class="px-2 py-1 bg-red-600 text-white rounded refuse-request" data-id="${req.id}">Refuser</button>
                        </div>
                    `;
                    receivedList.appendChild(li);
                });
                
                sent.forEach(req => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between bg-gray-700 rounded-lg p-2';
                    li.innerHTML = `<span class="text-white">${req.pseudo}</span>`;
                    sentList.appendChild(li);
                });
                
                // Actions accepter/refuser
                document.querySelectorAll('.accept-request').forEach(btn => {
                    btn.onclick = function() {
                        fetch('/friend/accept', { 
                            method: 'POST', 
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
                            body: 'id=' + btn.dataset.id 
                        })
                        .then(() => loadFriendRequests());
                    };
                });
                
                document.querySelectorAll('.refuse-request').forEach(btn => {
                    btn.onclick = function() {
                        fetch('/friend/refuse', { 
                            method: 'POST', 
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
                            body: 'id=' + btn.dataset.id 
                        })
                        .then(() => loadFriendRequests());
                    };
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des demandes:', error);
            });
    }

    // Fermer les modaux en cliquant à l'extérieur
    window.addEventListener('click', function(event) {
        const addFriendModal = document.getElementById('add-friend-modal');
        const friendRequestsModal = document.getElementById('friend-requests-modal');
        
        if (event.target === addFriendModal) {
            addFriendModal.classList.add('hidden');
        }
        if (event.target === friendRequestsModal) {
            friendRequestsModal.classList.add('hidden');
        }
    });
});
</script>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 