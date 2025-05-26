<?php
include_once(__DIR__ . '/../build/header.php');
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <link rel="stylesheet" href="/style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <style>
        #authentication-modal, #modal-createaccount, #modal-addgrade {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1a1a2e;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4a4a6a;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #5a5a7a;
        }
        @media (max-width: 1024px) {
            #messages {
                max-height: 40vh !important;
            }
        }
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }
            .grid {
                gap: 0.5rem;
            }
            #messages {
                max-height: 30vh !important;
            }
        }
    </style>
</head>
<body class="bg-[#0A0A23] min-h-screen">
<div class="container mx-auto px-2 py-2 md:px-4 md:py-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 h-[calc(100vh-12rem)]">
        <div class="bg-[#081225] rounded-lg p-2 md:p-3 md:col-span-1 flex flex-col min-h-0">
            <h2 class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3">Amis</h2>
            <div class="flex-1 overflow-y-auto min-h-0">
                <ul id="friend-list" class="space-y-2"></ul>
            </div>
            <div class="mt-2 md:mt-3 space-y-2">
                <button id="add-friend-btn" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 md:px-4 md:py-2">Ajouter un ami</button>
                <button id="friend-requests-btn" class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-2 md:px-4 md:py-2">Demandes d'amis</button>
            </div>
        </div>
        <div class="bg-[#081225] rounded-lg p-2 md:p-3 md:col-span-3 flex flex-col min-h-0">
            <div id="conversation-header" class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3"></div>
            <div id="messages" class="flex-1 overflow-y-auto space-y-3 mb-2 md:mb-3 pr-2 custom-scrollbar bg-[#10182A] rounded-lg" style="max-height:60vh;"></div>
            <form id="send-message-form" class="hidden">
                <div class="flex gap-2">
                    <input type="text" id="message-input" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" placeholder="Votre message..." autocomplete="off">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="add-friend-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-[#081225] rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-xl font-semibold text-white">Ajouter un ami</h3>
                <button type="button" id="close-add-friend" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
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
        <div class="relative bg-[#081225] rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-xl font-semibold text-white">Demandes d'amis</h3>
                <button type="button" id="close-friend-requests" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
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
$(document).ready(function() {
    let currentFriendId = null;
    let isRefreshing = false;
    let friendsInterval = setInterval(function() {
        if (!currentFriendId) refreshFriends();
    }, 5000);
    let currentLoadSession = 0;
    let autoRefreshTimeout = null;
    function setInputState(enabled) {
        $('#message-input').prop('disabled', !enabled);
        $('#send-message-form button[type="submit"]').prop('disabled', !enabled);
    }
    function showNoConversation() {
        $('#messages').html('<div class="flex justify-center items-center h-full text-gray-400 text-center">Cliquez sur un utilisateur pour commencer à discuter</div>');
        setInputState(false);
        $('#send-message-form').addClass('hidden');
        $('#conversation-header').text('');
    }
    function showEmptyConversation() {
        $('#messages').html('<div class="flex justify-center items-center h-full text-gray-400 text-center">Ceci est le début de votre conversation</div>');
    }
    function autoRefresh(friendId, sessionId) {
        if (!isRefreshing || sessionId !== currentLoadSession) return;
        loadMessages(friendId, sessionId);
        autoRefreshTimeout = setTimeout(function() {
            if (isRefreshing && sessionId === currentLoadSession) {
                autoRefresh(friendId, sessionId);
            }
        }, 2000);
    }
    function startAutoRefresh(friendId) {
        isRefreshing = true;
        currentLoadSession++;
        autoRefresh(friendId, currentLoadSession);
    }
    function stopAutoRefresh() {
        isRefreshing = false;
        if (autoRefreshTimeout) clearTimeout(autoRefreshTimeout);
    }
    function refreshFriends() {
        $.ajax({
            url: '/api/friends.php',
            method: 'GET',
            dataType: 'json',
            success: function(friends) {
                let list = '';
                friends.forEach(f => {
                    let unreadBadge = f.unread_count > 0 ? 
                        `<span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">${f.unread_count}</span>` : '';
                    list += `
                        <li data-id="${f.id}" data-pseudo="${f.pseudo}" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 cursor-pointer">
                            <span class="text-white">${f.pseudo}</span>
                            ${unreadBadge}
                        </li>`;
                });
                $('#friend-list').html(list);
            }
        });
    }
    function loadMessages(friendId, sessionId) {
        $.ajax({
            url: '/api/messages.php',
            method: 'GET',
            data: { friend_id: friendId },
            dataType: 'json',
            success: function(messages) {
                if (sessionId !== currentLoadSession) return;
                $('#messages').empty();
                if (messages.length === 0) {
                    showEmptyConversation();
                } else {
                    messages.forEach(function(m) {
                        let div = $(`
                            <div class="flex ${m.sent ? 'justify-end' : 'justify-start'}">
                                <div class="max-w-[70%] ${m.sent ? 'bg-blue-600' : 'bg-gray-700'} rounded-lg p-3">
                                    <div class="text-white">${m.content}</div>
                                    <div class="text-xs text-gray-300 mt-1">${m.time}</div>
                                </div>
                            </div>
                        `);
                        $('#messages').append(div);
                    });
                    $('#messages').scrollTop($('#messages')[0].scrollHeight);
                }
                $(`#friend-list li[data-id="${friendId}"] .inline-flex`).remove();
                refreshFriends();
                setInputState(true);
                $('#send-message-form').removeClass('hidden');
            }
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
        $('#add-friend-modal').removeClass('hidden');
    });
    $('#close-add-friend').click(function() {
        $('#add-friend-modal').addClass('hidden');
        $('#add-friend-feedback').text('');
    });
    $('#send-friend-request').click(function() {
        let pseudo = $('#add-friend-pseudo').val();
        $.post('/api/add_friend.php', {pseudo}, function(resp) {
            $('#add-friend-feedback').text(resp);
            refreshRequests();
        });
    });
    $('#friend-requests-btn').off('click').on('click', function() {
        refreshRequests();
        $('#friend-requests-modal').removeClass('hidden');
    });
    $('#close-friend-requests').off('click').on('click', function() {
        $('#friend-requests-modal').addClass('hidden');
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
        $('#friend-list li').removeClass('bg-gray-700');
        $(this).addClass('bg-gray-700');
        let id = $(this).data('id');
        let pseudo = $(this).data('pseudo');
        currentFriendId = id;
        $('#conversation-header').text('Conversation avec ' + pseudo);
        $('#send-message-form').removeClass('hidden');
        $('#messages').empty();
        setInputState(false);
        startAutoRefresh(id);
        loadMessages(id, currentLoadSession);
        $('#send-message-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            let msg = $('#message-input').val();
            $.post('/api/send_message.php', {to:id, message:msg}, function() {
                $('#message-input').val('');
                loadMessages(id, currentLoadSession);
            });
        });
    });
    $(window).on('beforeunload', function() {
        stopAutoRefresh();
        clearInterval(friendsInterval);
    });
    if ($('#friend-list li.selected').length > 0) {
        let selectedId = $('#friend-list li.selected').data('id');
        currentFriendId = selectedId;
        startAutoRefresh(selectedId);
    }
    showNoConversation();
    refreshFriends();
});
</script>
<?php
include_once(__DIR__ . '/../build/footer.php');
?>
