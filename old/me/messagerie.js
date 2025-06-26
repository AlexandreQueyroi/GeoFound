$(document).ready(function() {
    
    let currentFriendId = null;
    let isRefreshing = false;

    function autoRefresh(friendId) {
        if (!isRefreshing) {
            return;
        }
        
        loadMessages(friendId);
        
        setTimeout(function() {
            if (isRefreshing) {
                autoRefresh(friendId);
            }
        }, 2000);
    }

    function startAutoRefresh(friendId) {
        isRefreshing = true;
        autoRefresh(friendId);
    }

    function stopAutoRefresh() {
        isRefreshing = false;
    }

    function refreshFriends() {
        $.get('/api/friends.php', function(data) {
            let friends = JSON.parse(data);
            let list = '';
            friends.forEach(f => {
                list += `<li data-id="${f.id}" data-pseudo="${f.pseudo}">${f.pseudo}</li>`;
            });
            $('
        });
    }
    function refreshRequests() {
        $.get('/api/friend_requests.php', function(data) {
            let reqs = JSON.parse(data);
            let rec = reqs.received.map(r => `<li>${r.pseudo} <button class='accept' data-id='${r.id}'>Accepter</button> <button class='refuse' data-id='${r.id}'>Refuser</button></li>`).join('');
            let sent = reqs.sent.map(r => `<li>${r.pseudo}</li>`).join('');
            $('
            $('
        });
    }
    $('
        $('
    });
    $('
        $('
        $('
    });
    $('
        let pseudo = $('
        $.post('/api/add_friend.php', {pseudo}, function(resp) {
            $('
            refreshRequests();
        });
    });
    $('
        refreshRequests();
        $('
    });
    $('
        $('
    });
    $(document).on('click', '.accept', function() {
        let id = $(this).data('id');
        $.post('/api/accept_friend.php', {id}, function() {
            refreshFriends();
            refreshRequests();
        });
    });
    $(document).on('click', '.refuse', function() {
        let id = $(this).data('id');
        $.post('/api/refuse_friend.php', {id}, function() {
            refreshRequests();
        });
    });
    $(document).on('click', '
        stopAutoRefresh();
        
        $('
        $(this).addClass('selected');
        let id = $(this).data('id');
        let pseudo = $(this).data('pseudo');
        currentFriendId = id;
        $('
        $('
        loadMessages(id);
        startAutoRefresh(id);

        $('
            e.preventDefault();
            let msg = $('
            $.post('/api/send_message.php', {to:id, message:msg}, function() {
                $('
                loadMessages(id);
            });
        });
    });
    function loadMessages(friendId) {
        $.ajax({
            url: '/api/messages.php',
            method: 'GET',
            data: { friend_id: friendId },
            success: function(response) {
                $('
                let messages = JSON.parse(response);
                messages.forEach(function(m) {
                    let div = $('<div>').addClass('message ' + (m.sent ? 'sent' : 'received'));
                    let content = $('<div>').addClass('message-content').text(m.content);
                    let time = $('<div>').addClass('message-time').text(m.time);
                    div.append(content).append(time);
                    $('
                });
                $('
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement des messages:', error);
            }
        });
    }

    $(window).on('beforeunload', function() {
        stopAutoRefresh();
    });

    if ($('
        let selectedId = $('
        currentFriendId = selectedId;
        startAutoRefresh(selectedId);
    }

    refreshFriends();
}); 