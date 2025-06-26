<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>
<style>
.selected {
    background-color: 
}
</style>
<div class="container mx-auto px-2 py-2 md:px-4 md:py-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 h-[calc(100vh-12rem)]">
        <div class="bg-[
            <h2 class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3">Amis</h2>
            <div class="flex-1 overflow-y-auto min-h-0">
                <ul id="friend-list" class="space-y-2">
                    <?php foreach ($friends ?? [] as $f): ?>
                        <li data-id="<?= $f['id'] ?>" data-pseudo="<?= htmlspecialchars($f['pseudo']) ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 cursor-pointer<?php if (isset($selected_friend) && $selected_friend == $f['id']) echo ' selected'; ?>">
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
        <div class="bg-[
            <div id="conversation-header" class="text-white text-lg md:text-xl font-bold mb-2 md:mb-3">
                <?php
                if ($selected_friend) {
                    $friend_pseudo = '';
                    foreach ($friends as $f) {
                        if ($f['id'] == $selected_friend) {
                            $friend_pseudo = $f['pseudo'];
                            break;
                        }
                    }
                    echo 'Conversation avec ' . htmlspecialchars($friend_pseudo);
                } elseif (empty($friends)) {
                    echo '<span class="text-gray-400">Aucun ami</span>';
                }
                ?>
            </div>
            <div id="messages" class="flex-1 overflow-y-auto space-y-3 mb-2 md:mb-3 pr-2 custom-scrollbar bg-[
                <?php if ($selected_friend): ?>
                    <?php if (count($messages) === 0): ?>
                        <div class="flex justify-center items-center h-full text-gray-400 text-center">Ceci est le début de votre conversation</div>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <div class="flex <?= $m['sent'] ? 'justify-end' : 'justify-start' ?>">
                                <div class="max-w-[70%] <?= $m['sent'] ? 'bg-blue-600' : 'bg-gray-700' ?> rounded-lg p-3">
                                    <div class="text-white"><?= htmlspecialchars($m['content']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex justify-center items-center h-full text-gray-400 text-center">Cliquez sur un utilisateur pour commencer à discuter</div>
                <?php endif; ?>
            </div>
            <?php if ($selected_friend): ?>
            <form id="send-message-form" class="mt-2">
                <div class="flex gap-2">
                    <input type="text" id="message-input" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" placeholder="Votre message..." autocomplete="off">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Envoyer</button>
                </div>
            </form>
            <?php endif; ?>
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
    window.selectedFriendId = <?= $selected_friend ? intval($selected_friend) : 'null' ?>;
</script>
<script src="https:
<script src="/assets/js/messagerie.js"></script>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 