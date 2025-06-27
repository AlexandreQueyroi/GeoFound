<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="container">
    <div class="messaging-container">
        <div class="friends-sidebar">
            <div class="friends-header">
                <h2>Amis</h2>
                <button id="add-friend-btn" class="btn btn-primary">Ajouter</button>
                <button id="friend-requests-btn" class="btn btn-info">Demandes</button>
            </div>
            <ul id="friend-list"></ul>
        </div>
        
        <div class="conversation-container">
            <h2 id="conversation-header">Sélectionnez un ami pour démarrer une conversation</h2>
            <div id="messages" class="messages-container"></div>
            <form id="send-message-form" style="display: none;">
                <div class="input-group">
                    <input type="text" id="message-input" class="form-control" placeholder="Votre message..." required>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour ajouter un ami -->
<div id="add-friend-modal" class="modal">
    <div class="modal-content">
        <h3>Ajouter un ami</h3>
        <div class="form-group">
            <input type="text" id="add-friend-pseudo" class="form-control" placeholder="Pseudo">
        </div>
        <div id="add-friend-feedback" class="feedback"></div>
        <div class="modal-buttons">
            <button id="send-friend-request" class="btn btn-primary">Envoyer</button>
            <button id="close-add-friend" class="btn btn-secondary">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal pour les demandes d'amis -->
<div id="friend-requests-modal" class="modal">
    <div class="modal-content">
        <h3>Demandes d'amis</h3>
        <div class="requests-section">
            <h4>Demandes reçues</h4>
            <ul id="received-requests"></ul>
        </div>
        <div class="requests-section">
            <h4>Demandes envoyées</h4>
            <ul id="sent-requests"></ul>
        </div>
        <button id="close-friend-requests" class="btn btn-secondary">Fermer</button>
    </div>
</div>

<style>
.messaging-container {
    display: flex;
    gap: 20px;
    height: calc(100vh - 100px);
    margin: 20px;
}

.friends-sidebar {
    width: 300px;
    border-right: 1px solid;
    padding: 10px;
}

.conversation-container {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #FFFFFF;
    margin-bottom: 20px;
    border-radius: 8px;
}

.message {
    margin: 10px 0;
    max-width: 70%;
}

.message.sent {
    margin-left: auto;
    background: #FFFFFF;
    color: white;
    border-radius: 15px 15px 0 15px;
    padding: 10px 15px;
}

.message.received {
    margin-right: auto;
    background: white;
    border: 1px solid;
    border-radius: 15px 15px 15px 0;
    padding: 10px 15px;
}

.message-time {
    font-size: 0.8em;
    opacity: 0.7;
    margin-top: 5px;
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid 
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    width: 90%;
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
}

.requests-section {
    margin: 20px 0;
}
</style>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 