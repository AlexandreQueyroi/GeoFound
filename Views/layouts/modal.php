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
                        <a href="#" data-modal-hide="authentication-modal" data-modal-target="modal-createaccount" data-modal-toggle="modal-createaccount" class="text-blue-600 hover:underline dark:text-blue-400">Créer votre compte</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div role="dialog" id="modal-createaccount" tabindex="-1" aria-hidden="true"
    class="hidden modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Créer un compte
                </h3>
                <button type="button"
                    class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="modal-createaccount">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http:
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Fermer la Modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form action="/register" method="POST" class="space-y-4">
                    <div>
                        <label for="newuser" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Identifiant
                        </label>
                        <input type="text" name="newuser" id="newuser" oninput="checkModalIsValid()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            placeholder="Identifiant" required />
                        <label for="newmail" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Adresse Mail
                        </label>
                        <input type="text" name="newmail" id="newmail" oninput="checkModalIsValid()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            placeholder="adresse@adresse.com" required />
                        <label for="newpass" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="newpass" id="newpass" oninput="checkModalIsValid()"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                placeholder="••••••••" required />
                            <button type="button" onclick="togglePasswordVisibility('newpass','icon-newpass')"
                                class="absolute top-2 right-2 z-10 px-2 py-1 rounded shadow">
                                <span id="icon-newpass" class="iconify" data-icon="tabler:eye-closed"></span>
                            </button>
                        </div>

                        <label for="newpass_confirm"
                            class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Confirmation du Mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="newpass_confirm" id="newpass_confirm"
                                oninput="checkModalIsValid()"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                placeholder="••••••••" required />
                            <button type="button"
                                onclick="togglePasswordVisibility('newpass_confirm','icon-newpass-confirm')"
                                class="absolute top-2 right-2 z-10 px-2 py-1 rounded shadow">
                                <span id="icon-newpass-confirm" class="iconify" data-icon="tabler:eye-closed"></span>
                            </button>
                        </div>
                    </div>
                    <label for="captcha-answer"
                        class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Captcha - Résolvez la question ci-dessous
                    </label>
                    <div class="mb-4">
                        <p id="captcha-question" class="text-sm font-medium text-gray-900 dark:text-white mb-2"></p>
                        <div class="flex gap-2">
                            <input type="text" id="captcha-answer" class="w-3/4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 
                                    dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Votre réponse"
                                required />
                            <button type="button" id="check-captcha" class="w-1/4 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none 
                                    focus:ring-green-300 font-medium rounded-lg text-sm py-2.5
                                    text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Valider
                            </button>
                        </div>
                        <p id="response" class="text-sm mt-2 text-red-500 dark:text-red-400"></p>
                    </div>

                    <script>
                        fetchCaptcha();
                        document.getElementById('check-captcha').addEventListener('click', checkCaptcha);
                    </script>
                    <button type="submit" id="submitBtn" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none 
                        focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5
                        text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        onclick="if (!checkModalIsValidConfirm(event)) return false;">
                        Créer mon compte
                    </button>
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

<!-- Modal Flowbite pour l'ajout de post -->
<div id="post-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 flex items-center justify-center">
  <div class="relative w-full max-w-2xl max-h-full">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
      <!-- Modal header -->
      <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Nouveau Post
        </h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="post-modal">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http:
          <span class="sr-only">Fermer</span>
        </button>
      </div>
      <!-- Modal body -->
      <div class="p-6 space-y-6">
        <form id="postForm" enctype="multipart/form-data">
          <div class="mb-4">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Titre *</label>
            <input type="text" id="title" name="title" class="form-control w-full" required>
          </div>
          <div class="mb-4">
            <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description *</label>
            <textarea id="content" name="content" class="form-control w-full" rows="4" required></textarea>
          </div>
          <div class="mb-4">
            <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Adresse *</label>
            <input type="text" id="address" name="address" class="form-control w-full" placeholder="Entrez une adresse..." required>
          </div>
          <div class="mb-4">
            <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image (optionnel)</label>
            <input type="file" id="image" name="image" class="form-control w-full" accept="image/*">
          </div>
          <input type="hidden" id="latitude" name="latitude" value="">
          <input type="hidden" id="longitude" name="longitude" value="">
        </form>
      </div>
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button type="button" class="btn btn-secondary" data-modal-hide="post-modal">Annuler</button>
        <button type="button" class="btn btn-primary" onclick="submitPostForm()">Créer le post</button>
      </div>
    </div>
  </div>
</div>

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