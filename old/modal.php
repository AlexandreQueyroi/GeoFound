<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<script src="/modal.js"></script>
<div id="authentication-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Connexion à votre compte
                </h3>
                <button type="button"
                    class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="authentication-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http:
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Fermer la Modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form class="space-y-4" id="connection-form" action="<?php echo "/action/userConnection.php" ?>"
                    method="POST">
                    <div>
                        <label for="pseudo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Identifiant ou adresse email
                        </label>
                        <input type="text" name="pseudo" id="pseudo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Identifiant/adresse@adresse.com" required />
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Mot de passe
                        </label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            required />
                    </div>
                    <div class="flex justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember" type="checkbox" value=""
                                    class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                            </div>
                            <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="
                            Mot de passe oublié?
                        </a>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Se
                        connecter</button>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                        Pas encore inscrit? <a href="
                            data-modal-hide="authentication-modal" data-modal-target="modal-createaccount"
                            data-modal-toggle="modal-createaccount">Créer votre compte</a>
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
                <form action="<?php echo "/action/userCreate.php" ?>" method="POST" class="space-y-4">
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
<div role="dialog" id="modal-addgrade" tabindex="-1" aria-hidden="true"
    class="hidden modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Ajouter un grade
                </h3>
                <button type="button"
                    class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="modal-addgrade">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http:
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Fermer la Modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form action="<?php echo "/action/rankCreate.php" ?>" method="POST" class="space-y-4">
                    <div>
                        <label for="grade-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nom du grade
                        </label>
                        <input type="text" name="grade-name" id="grade-name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            placeholder="Nom du grade" required />
                    </div>
                    <div>
                        <label for="grade-color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Couleur
                        </label>
                        <input type="color" name="grade-color" id="grade-color"
                            class="w-16 h-10 p-0 border-none bg-transparent cursor-pointer" required />
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Ajouter le grade
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>