<!-- <script src="assets/js/modal.js"></script> -->
<div id="authentication-modal" tabindex="-1" aria-hidden="true"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 flex items-center justify-center">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Connexion à votre compte
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="authentication-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Fermer la Modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <!-- Zone d'affichage des erreurs -->
                <?php if (isset($_SESSION['login_error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium"><?php echo htmlspecialchars($_SESSION['login_error']); ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['login_error']); endif; ?>
                
                <form class="space-y-4" id="connection-form" action="/auth/login" method="POST">
                    <div>
                        <label for="pseudo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Identifiant ou adresse email
                        </label>
                        <input type="text" name="pseudo" id="pseudo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Identifiant ou adresse@email.com" required />
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Mot de passe
                        </label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            required />
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <input id="remember" type="checkbox" value=""
                                class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                            <label for="remember" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:underline dark:text-blue-400">Mot de passe oublié ?</a>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Se
                        connecter</button>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-300 text-center">
                        Pas encore inscrit ?
                        <a href="/auth/register" class="text-blue-600 hover:underline dark:text-blue-400">Créer votre compte</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modale ajout ami pour la messagerie -->
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

<!-- Modale demandes d'amis pour la messagerie -->
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

<!-- Modal de création de post -->
<div id="post-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-2xl border border-gray-700 transform transition-all duration-300 scale-95 opacity-0" id="post-modal-content">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-500 bg-opacity-20 rounded-lg">
                        <iconify-icon icon="tabler:plus" class="text-blue-400" width="20" height="20"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Créer un nouveau post</h3>
                </div>
                <button onclick="hidePostModal()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors">
                    <iconify-icon icon="tabler:x" width="20" height="20"></iconify-icon>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 space-y-6">
                <form id="postForm" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Titre *</label>
                            <input type="text" id="title" name="title" 
                                   class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="Donnez un titre à votre post..." required>
                        </div>
                        
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-300 mb-2">Description *</label>
                            <textarea id="content" name="content" rows="4" 
                                      class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                      placeholder="Décrivez votre découverte ou partagez votre expérience..." required></textarea>
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-300 mb-2">Adresse *</label>
                            <input type="text" id="address" name="address" 
                                   class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="Entrez l'adresse du lieu..." required>
                        </div>
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-300 mb-2">Image</label>
                            <div class="relative">
                                <input type="file" id="image" name="image" accept="image/*"
                                       class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Formats acceptés : JPG, PNG, GIF (max 5MB)</p>
                        </div>
                        
                        <input type="hidden" id="latitude" name="latitude" value="">
                        <input type="hidden" id="longitude" name="longitude" value="">
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-700">
                <button onclick="hidePostModal()" 
                        class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium">
                    Annuler
                </button>
                <button onclick="submitPostForm()" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                    <iconify-icon icon="tabler:send" width="16" height="16"></iconify-icon>
                    <span>Créer le post</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Container pour les notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Modal de gestion des permissions et maintenance -->
<div id="permission-maintenance-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium text-white">Gestion des Permissions & Maintenance</h3>
                <button id="close-permission-modal" class="text-gray-400 hover:text-white">
                    <iconify-icon icon="tabler:x" width="24" height="24"></iconify-icon>
                </button>
            </div>

            <!-- Onglets -->
            <div class="border-b border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button class="tab-button active text-blue-400 border-b-2 border-blue-400 py-2 px-1 text-sm font-medium" data-tab="maintenance">
                        Maintenance
                    </button>
                    <button class="tab-button text-gray-400 border-b-2 border-transparent py-2 px-1 text-sm font-medium hover:text-gray-300" data-tab="permissions">
                        Permissions Page
                    </button>
                    <button class="tab-button text-gray-400 border-b-2 border-transparent py-2 px-1 text-sm font-medium hover:text-gray-300" data-tab="quick-actions">
                        Actions Rapides
                    </button>
                </nav>
            </div>

            <!-- Onglet Maintenance -->
            <div id="maintenance-tab" class="tab-content">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-white mb-4">Gestion de la Maintenance</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Formulaire d'ajout/modification -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h5 class="text-white font-medium mb-3">Configuration de la page</h5>
                            <form id="maintenance-form">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Chemin de la page</label>
                                    <input type="text" id="page-path" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm" 
                                           placeholder="/exemple/page" required>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Nom de la page</label>
                                    <input type="text" id="page-name" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm" 
                                           placeholder="Nom affiché" required>
                                </div>
                                <div class="mb-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="is-maintenance" class="mr-2">
                                        <span class="text-sm font-medium text-gray-300">En maintenance</span>
                                    </label>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Message de maintenance</label>
                                    <textarea id="maintenance-message" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm h-20" 
                                              placeholder="Message affiché aux utilisateurs"></textarea>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                        Sauvegarder
                                    </button>
                                    <button type="button" id="reset-maintenance-form" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                        Réinitialiser
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des pages en maintenance -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h5 class="text-white font-medium mb-3">Pages en maintenance</h5>
                            <div id="maintenance-list" class="space-y-2 max-h-64 overflow-y-auto">
                                <p class="text-gray-400 text-sm">Chargement...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Permissions -->
            <div id="permissions-tab" class="tab-content hidden">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-white mb-4">Permissions de Page</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Formulaire d'ajout de permission -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h5 class="text-white font-medium mb-3">Ajouter une permission</h5>
                            <form id="page-permission-form">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Page</label>
                                    <input type="text" id="permission-page-path" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm" 
                                           placeholder="/exemple/page" required>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Permission requise</label>
                                    <select id="permission-select" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                                    <textarea id="permission-description" class="w-full bg-gray-600 text-white px-3 py-2 rounded-lg text-sm h-16" 
                                              placeholder="Description de la permission"></textarea>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                        Ajouter
                                    </button>
                                    <button type="button" id="reset-permission-form" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                        Réinitialiser
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des permissions de page -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h5 class="text-white font-medium mb-3">Permissions configurées</h5>
                            <div id="page-permissions-list" class="space-y-2 max-h-64 overflow-y-auto">
                                <p class="text-gray-400 text-sm">Chargement...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Actions Rapides -->
            <div id="quick-actions-tab" class="tab-content hidden">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-white mb-4">Actions Rapides</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Actions de maintenance -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="space-y-3">
                                <h5 class="text-white font-medium mb-3">Maintenance</h5>
                                <button id="quick-maintenance-all" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Mettre tout le site en maintenance
                                </button>
                                <button id="quick-maintenance-none" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Désactiver toute la maintenance
                                </button>
                                <button id="quick-maintenance-current" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Maintenance page actuelle
                                </button>
                            </div>
                        </div>

                        <!-- Actions de permissions -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="space-y-3">
                                <h5 class="text-white font-medium mb-3">Permissions</h5>
                                <button id="quick-permission-public" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Rendre la page publique
                                </button>
                                <button id="quick-permission-admin" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Admin uniquement
                                </button>
                                <button id="quick-permission-logged" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Utilisateurs connectés
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/permissions.js"></script>

<!-- Modal de signalement -->
<div id="report-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-md border border-gray-700 transform transition-all duration-300 scale-95 opacity-0" id="report-modal-content">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-red-500 bg-opacity-20 rounded-lg">
                        <iconify-icon icon="tabler:flag" class="text-red-400" width="20" height="20"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Signaler un contenu</h3>
                </div>
                <button onclick="hideReportModal()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors">
                    <iconify-icon icon="tabler:x" width="20" height="20"></iconify-icon>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Motif du signalement *</label>
                    <select id="report-reason" class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                        <option value="">Sélectionnez un motif</option>
                        <option value="Contenu inapproprié">🚫 Contenu inapproprié</option>
                        <option value="Spam">📧 Spam</option>
                        <option value="Harcèlement">😡 Harcèlement</option>
                        <option value="Violence">⚔️ Violence</option>
                        <option value="Contenu illégal">🚨 Contenu illégal</option>
                        <option value="Fausses informations">🤥 Fausses informations</option>
                        <option value="Autre">❓ Autre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Détails supplémentaires</label>
                    <textarea id="report-details" rows="4" 
                              placeholder="Décrivez le problème en détail pour nous aider à mieux comprendre la situation..."
                              class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-2">Ces informations nous aident à traiter votre signalement plus efficacement.</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-700">
                <button onclick="hideReportModal()" 
                        class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium">
                    Annuler
                </button>
                <button onclick="submitReport()" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center space-x-2">
                    <iconify-icon icon="tabler:send" width="16" height="16"></iconify-icon>
                    <span>Envoyer le signalement</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Animation pour la modal de signalement
function showReportModal() {
    const modal = document.getElementById('report-modal');
    const content = document.getElementById('report-modal-content');
    
    if (modal && content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
}

function hideReportModal() {
    const modal = document.getElementById('report-modal');
    const content = document.getElementById('report-modal-content');
    
    if (modal && content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            // Réinitialiser le formulaire
            document.getElementById('report-reason').value = '';
            document.getElementById('report-details').value = '';
        }, 300);
    }
}

// Remplacer les anciennes fonctions par les nouvelles
function openReportModal(type, targetId) {
    const modal = document.getElementById('report-modal');
    if (modal) {
        modal.dataset.type = type;
        modal.dataset.targetId = targetId;
        showReportModal();
    }
}
</script> 