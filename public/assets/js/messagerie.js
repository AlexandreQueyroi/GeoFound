$(document).ready(function() {
    console.log("Messagerie.js chargé");
    let currentFriendId = null;
    let refreshIntervalId = null;

    function startAutoRefresh(friendId) {
        stopAutoRefresh();
        refreshIntervalId = setInterval(function() {
            loadMessages(friendId);
        }, 5000);
        loadMessages(friendId);
    }

    function stopAutoRefresh() {
        if (refreshIntervalId !== null) {
            clearInterval(refreshIntervalId);
            refreshIntervalId = null;
        }
    }

    function refreshFriends() {
        console.log("Rafraîchissement de la liste d'amis");
        $.get('/message/friends', function(data) {
            console.log("Données reçues:", data);
            let friends = typeof data === 'string' ? JSON.parse(data) : data;
            let list = '';
            friends.forEach(f => {
                const unreadBadge = f.unread_count > 0 ? 
                    `<span class="bg-red-500 text-white text-xs font-medium px-2.5 py-0.5 rounded-full">${f.unread_count}</span>` : '';
                list += `
                    <li data-id="${f.id}" data-pseudo="${f.pseudo}" 
                        class="flex items-center justify-between p-2.5 rounded-lg cursor-pointer transition-colors duration-200 
                        ${currentFriendId == f.id ? 'bg-accent' : 'bg-secondary hover:bg-gray-700'}">
                        <span class="truncate text-white">${f.pseudo}</span>
                        ${unreadBadge}
                    </li>`;
            });
            $('
            if (currentFriendId) {
                let pseudo = $(`
                $('
            }
        }).fail(function(err) {
            console.error("Erreur lors du chargement des amis:", err);
        });
    }

    function refreshRequests() {
        console.log("Rafraîchissement des demandes d'amis");
        $.get('/friend/requests', function(data) {
            console.log("Demandes reçues:", data);
            let reqs = typeof data === 'string' ? JSON.parse(data) : data;
            let rec = reqs.received.map(r => `
                <li class="flex items-center justify-between p-2.5 bg-secondary rounded-lg">
                    <span class="text-white">${r.pseudo}</span>
                    <div class="flex gap-2">
                        <button class="accept bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded-md transition-colors duration-200" data-id="${r.id}">Accepter</button>
                        <button class="refuse bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md transition-colors duration-200" data-id="${r.id}">Refuser</button>
                    </div>
                </li>
            `).join('');
            let sent = reqs.sent.map(r => `
                <li class="flex items-center justify-between p-2.5 bg-secondary rounded-lg">
                    <span class="text-white">${r.pseudo}</span>
                    <span class="text-gray-400 text-sm">En attente</span>
                </li>
            `).join('');
            $('
            $('
        }).fail(function(err) {
            console.error("Erreur lors du chargement des demandes:", err);
        });
    }

    $('
        console.log("Ouverture du modal d'ajout d'ami");
        const modal = $('
        modal.removeClass('hidden').addClass('show');
    });

    $('
        const modal = $('
        modal.removeClass('show');
        setTimeout(() => modal.addClass('hidden'), 300);
        $('
    });

    $('
        let pseudo = $('
        if (!pseudo.trim()) {
            $('
            return;
        }
        console.log("Envoi d'une demande d'ami à", pseudo);
        $.post('/friend/add', {pseudo}, function(resp) {
            $('
            refreshRequests();
            $('
        }).fail(function(err) {
            console.error("Erreur lors de l'envoi de la demande:", err);
            $('
        });
    });

    $('
        console.log("Ouverture du modal des demandes d'amis");
        refreshRequests();
        const modal = $('
        modal.removeClass('hidden').addClass('show');
    });

    $('
        const modal = $('
        modal.removeClass('show');
        setTimeout(() => modal.addClass('hidden'), 300);
    });

    $(document).on('click', '.accept', function() {
        let id = $(this).data('id');
        console.log("Acceptation de la demande", id);
        $.post('/friend/accept', {id}, function() {
            refreshFriends();
            refreshRequests();
        }).fail(function(err) {
            console.error("Erreur lors de l'acceptation:", err);
        });
    });

    $(document).on('click', '.refuse', function() {
        let id = $(this).data('id');
        console.log("Refus de la demande", id);
        $.post('/friend/refuse', {id}, function() {
            refreshRequests();
        }).fail(function(err) {
            console.error("Erreur lors du refus:", err);
        });
    });

    $(document).on('click', '
        stopAutoRefresh();
        let id = $(this).data('id');
        let pseudo = $(this).data('pseudo');
        console.log("Sélection de la conversation avec", pseudo, "(ID:", id, ")");
        currentFriendId = id;
        $('
        $('
        startAutoRefresh(id);
        $(this).find('.bg-red-500').remove();
    });

    $('
        e.preventDefault();
        let msg = $('
        if (!msg.trim() || !currentFriendId) return;
        
        console.log("Envoi d'un message à", currentFriendId, ":", msg);
        $.post('/message/send', {to: currentFriendId, message: msg}, function() {
            $('
            loadMessages(currentFriendId);
        }).fail(function(err) {
            console.error("Erreur lors de l'envoi du message:", err);
        });
    });

    function formatMessageTime(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = now - date;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        
        if (days === 0) {
            return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        } else if (days === 1) {
            return 'Hier ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        } else {
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) + ' ' + 
                   date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
    }

    function loadMessages(friendId) {
        console.log("Chargement des messages avec", friendId);
        $.ajax({
            url: '/message/conversation',
            method: 'GET',
            data: { friend_id: friendId },
            success: function(response) {
                console.log("Messages reçus:", response);
                $('
                let messages = typeof response === 'string' ? JSON.parse(response) : response;
                messages.forEach(function(m) {
                    const messageClasses = m.sent ? 
                        'self-end bg-accent rounded-tl-xl rounded-tr-xl rounded-bl-xl message' :
                        'self-start bg-secondary rounded-tl-xl rounded-tr-xl rounded-br-xl message';
                    let div = $('<div>').addClass(`max-w-[70%] p-3 ${messageClasses}`);
                    let content = $('<div>').addClass('text-white mb-1').text(m.content);
                    let time = $('<div>').addClass('text-xs text-gray-300 text-right').text(formatMessageTime(m.time));
                    div.append(content).append(time);
                    $('
                });
                $('
                refreshFriends();
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement des messages:', error);
            }
        });
    }

    $(window).on('beforeunload', function() {
        stopAutoRefresh();
    });

    console.log("Initialisation de la messagerie");
    refreshFriends();

    $(document).on('click', '.modal', function(e) {
        if ($(e.target).hasClass('modal')) {
            const modal = $(this);
            modal.removeClass('show');
            setTimeout(() => modal.addClass('hidden'), 300);
        }
    });
}); 