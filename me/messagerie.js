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
            $('#friend-list').html(list);
        });
    }
    function refreshRequests() {
        $.get('/api/friend_requests.php', function(data) {
            let reqs = JSON.parse(data);
            let rec = reqs.received.map(r => `<li>${r.pseudo} <button class='accept' data-id='${r.id}'>Accepter</button> <button class='refuse' data-id='${r.id}'>Refuser</button></li>`).join('');
            let sent = reqs.sent.map(r => `<li>${r.pseudo}</li>`).join('');
            $('#received-requests').html(rec);
            $('#sent-requests').html(sent);
        });
    }
    $('#add-friend-btn').click(function() {
        $('#add-friend-modal').show();
    });
    $('#close-add-friend').click(function() {
        $('#add-friend-modal').hide();
        $('#add-friend-feedback').text('');
    });
    $('#send-friend-request').click(function() {
        let pseudo = $('#add-friend-pseudo').val();
        $.post('/api/add_friend.php', {pseudo}, function(resp) {
            $('#add-friend-feedback').text(resp);
            refreshRequests();
        });
    });
    $('#friend-requests-btn').click(function() {
        refreshRequests();
        $('#friend-requests-modal').show();
    });
    $('#close-friend-requests').click(function() {
        $('#friend-requests-modal').hide();
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
    $(document).on('click', '#friend-list li', function() {
        stopAutoRefresh();
        
        $('#friend-list li').removeClass('selected');
        $(this).addClass('selected');
        let id = $(this).data('id');
        let pseudo = $(this).data('pseudo');
        currentFriendId = id;
        $('#conversation-header').text('Conversation avec ' + pseudo);
        $('#send-message-form').show();
        loadMessages(id);
        startAutoRefresh(id);

        $('#send-message-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            let msg = $('#message-input').val();
            $.post('/api/send_message.php', {to:id, message:msg}, function() {
                $('#message-input').val('');
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
                $('#messages').empty();
                let messages = JSON.parse(response);
                messages.forEach(function(m) {
                    let div = $('<div>').addClass('message ' + (m.sent ? 'sent' : 'received'));
                    let content = $('<div>').addClass('message-content').text(m.content);
                    let time = $('<div>').addClass('message-time').text(m.time);
                    div.append(content).append(time);
                    $('#messages').append(div);
                });
                $('#messages').scrollTop($('#messages')[0].scrollHeight);
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement des messages:', error);
            }
        });
    }

    $(window).on('beforeunload', function() {
        stopAutoRefresh();
    });

    if ($('#friend-list li.selected').length > 0) {
        let selectedId = $('#friend-list li.selected').data('id');
        currentFriendId = selectedId;
        startAutoRefresh(selectedId);
    }

    refreshFriends();
}); 