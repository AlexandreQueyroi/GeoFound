<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modaux</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-8">
    <h1 class="text-2xl font-bold mb-8">Test des Modaux</h1>
    
    <!-- Boutons de test -->
    <div class="space-y-4">
        <button id="add-friend-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Ajouter un ami</button>
        <button id="friend-requests-btn" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Demandes d'amis</button>
    </div>

    <!-- Modal Ajouter un ami -->
    <div id="add-friend-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Ajouter un ami</h3>
                <button id="close-add-friend" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            <input type="text" id="add-friend-pseudo" class="w-full bg-gray-700 border border-gray-600 text-white px-3 py-2 rounded mb-4" placeholder="Pseudo de l'ami">
            <button id="send-friend-request" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Envoyer la demande</button>
            <div id="add-friend-feedback" class="mt-4 text-sm"></div>
        </div>
    </div>

    <!-- Modal Demandes d'amis -->
    <div id="friend-requests-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Demandes d'amis</h3>
                <button id="close-friend-requests" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            <div class="mb-4">
                <h4 class="text-lg font-semibold mb-2">Demandes reçues</h4>
                <ul id="received-requests" class="space-y-2"></ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-2">Demandes envoyées</h4>
                <ul id="sent-requests" class="space-y-2"></ul>
            </div>
        </div>
    </div>

    <script>
        // Gestion des modaux
        document.getElementById('add-friend-btn').addEventListener('click', function() {
            document.getElementById('add-friend-modal').classList.remove('hidden');
        });
        
        document.getElementById('close-add-friend').addEventListener('click', function() {
            document.getElementById('add-friend-modal').classList.add('hidden');
        });

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
            
            document.getElementById('add-friend-feedback').textContent = 'Demande envoyée à ' + pseudo;
            document.getElementById('add-friend-pseudo').value = '';
            
            setTimeout(() => {
                document.getElementById('add-friend-modal').classList.add('hidden');
            }, 2000);
        });

        // Charger les demandes d'amis (simulation)
        function loadFriendRequests() {
            const receivedList = document.getElementById('received-requests');
            const sentList = document.getElementById('sent-requests');
            
            receivedList.innerHTML = '<li class="text-gray-400">Aucune demande reçue</li>';
            sentList.innerHTML = '<li class="text-gray-400">Aucune demande envoyée</li>';
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
    </script>
</body>
</html> 