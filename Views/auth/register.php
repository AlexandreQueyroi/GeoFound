<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <img src="/assets/img/logo.png" alt="Logo" class="w-16 h-16">
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Créer votre compte
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Rejoignez la communauté GeoFound et commencez votre aventure !
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl rounded-lg sm:px-10 border border-gray-200 dark:border-gray-700">
            
            
            <?php if (isset($_SESSION['register_errors'])): ?>
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <iconify-icon icon="tabler:alert-circle" class="text-red-400 mt-0.5" width="20" height="20"></iconify-icon>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Erreurs de validation
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <?php foreach ($_SESSION['register_errors'] as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['register_errors']); ?>
            <?php endif; ?>

            
            <?php if (isset($_SESSION['register_success'])): ?>
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex">
                        <iconify-icon icon="tabler:check-circle" class="text-green-400 mt-0.5" width="20" height="20"></iconify-icon>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                Compte créé avec succès !
                            </h3>
                            <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                <?php echo htmlspecialchars($_SESSION['register_success']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['register_success']); ?>
            <?php endif; ?>

            
            <?php if (isset($_SESSION['register_warning'])): ?>
                <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <iconify-icon icon="tabler:alert-triangle" class="text-yellow-400 mt-0.5" width="20" height="20"></iconify-icon>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Attention
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <?php echo htmlspecialchars($_SESSION['register_warning']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['register_warning']); ?>
            <?php endif; ?>

            
            <?php if (isset($_SESSION['resend_success'])): ?>
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <iconify-icon icon="tabler:mail-check" class="text-blue-400 mt-0.5" width="20" height="20"></iconify-icon>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Email envoyé
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <?php echo htmlspecialchars($_SESSION['resend_success']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['resend_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['resend_error'])): ?>
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <iconify-icon icon="tabler:alert-circle" class="text-red-400 mt-0.5" width="20" height="20"></iconify-icon>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Erreur
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <?php echo htmlspecialchars($_SESSION['resend_error']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['resend_error']); ?>
            <?php endif; ?>

            
            <form class="space-y-6" action="/auth/register" method="POST" id="registerForm">
                <div>
                    <label for="newuser" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nom d'utilisateur *
                    </label>
                    <div class="mt-1">
                        <input id="newuser" name="newuser" type="text" required 
                               value="<?php echo htmlspecialchars($_SESSION['register_data']['username'] ?? ''); ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                               placeholder="Votre nom d'utilisateur">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        3-20 caractères, lettres, chiffres et underscores uniquement
                    </p>
                </div>

                <div>
                    <label for="newmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Adresse email *
                    </label>
                    <div class="mt-1">
                        <input id="newmail" name="newmail" type="email" required 
                               value="<?php echo htmlspecialchars($_SESSION['register_data']['email'] ?? ''); ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                               placeholder="votre@email.com">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Un email de validation sera envoyé à cette adresse
                    </p>
                </div>

                <div>
                    <label for="newpass" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Mot de passe *
                    </label>
                    <div class="mt-1">
                        <input id="newpass" name="newpass" type="password" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                               placeholder="••••••••">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Au moins 6 caractères
                    </p>
                </div>

                <div>
                    <label for="confirmpass" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirmer le mot de passe *
                    </label>
                    <div class="mt-1">
                        <input id="confirmpass" name="confirmpass" type="password" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        J'accepte les <a href="/terms" class="text-blue-600 hover:text-blue-500">conditions d'utilisation</a> et la <a href="/privacy" class="text-blue-600 hover:text-blue-500">politique de confidentialité</a>
                    </label>
                </div>

                
                <div>
                    <label for="captcha-answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Captcha - Résolvez la question ci-dessous *
                    </label>
                    <div class="mt-1">
                        <p id="captcha-question" class="text-sm font-medium text-gray-900 dark:text-white mb-2 bg-gray-100 dark:bg-gray-700 p-3 rounded-lg"></p>
                        <div class="flex gap-2">
                            <input type="text" id="captcha-answer" name="captcha-answer" required
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                   placeholder="Votre réponse">
                            <button type="button" id="check-captcha" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                Valider
                            </button>
                        </div>
                        <p id="captcha-response" class="mt-2 text-sm"></p>
                    </div>
                </div>

                <div>
                    <button type="submit" id="submitBtn" disabled
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <iconify-icon icon="tabler:user-plus" class="mr-2" width="16" height="16"></iconify-icon>
                        Créer mon compte
                    </button>
                </div>
            </form>

            
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Vous n'avez pas reçu l'email de validation ?
                </h3>
                <form action="/auth/resend-verification" method="POST" class="flex space-x-2">
                    <input type="email" name="email" placeholder="Votre email" required
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Renvoyer
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Déjà un compte ? 
                    <a href="/" class="font-medium text-blue-600 hover:text-blue-500">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('newpass');
    const confirmPassword = document.getElementById('confirmpass');
    
    
    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    
    const username = document.getElementById('newuser');
    username.addEventListener('input', function() {
        const value = this.value;
        if (value.length > 0 && !/^[a-zA-Z0-9_]+$/.test(value)) {
            this.setCustomValidity('Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores');
        } else {
            this.setCustomValidity('');
        }
    });
    
    
    let correctAnswer = "";
    let validCaptcha = false;
    
    async function fetchCaptcha() {
        try {
            const response = await fetch("/api/captcha");
            const data = await response.json();
            correctAnswer = data.answer;
            document.getElementById("captcha-question").textContent = data.question;
            document.getElementById("captcha-answer").value = "";
            document.getElementById("captcha-response").textContent = "";
        } catch (error) {
            console.error("Erreur lors du chargement du captcha:", error);
            document.getElementById("captcha-response").textContent = "Erreur lors du chargement du captcha.";
        }
    }
    
    function checkCaptcha() {
        let userInput = document.getElementById("captcha-answer").value.trim();
        const responseElement = document.getElementById("captcha-response");
        const submitBtn = document.getElementById("submitBtn");
        
        if (!userInput) {
            responseElement.textContent = "❌ Veuillez entrer une réponse.";
            responseElement.className = "mt-2 text-sm text-red-600 dark:text-red-400";
            validCaptcha = false;
            submitBtn.disabled = true;
            return;
        }
        
        if (userInput.toLowerCase() === correctAnswer.toLowerCase()) {
            responseElement.textContent = "✅ Réponse correcte";
            responseElement.className = "mt-2 text-sm text-green-600 dark:text-green-400";
            validCaptcha = true;
            submitBtn.disabled = false;
        } else {
            responseElement.textContent = "❌ Réponse incorrecte";
            responseElement.className = "mt-2 text-sm text-red-600 dark:text-red-400";
            validCaptcha = false;
            submitBtn.disabled = true;
            fetchCaptcha();
        }
    }
    
    
    fetchCaptcha();
    
    
    document.getElementById("check-captcha").addEventListener("click", checkCaptcha);
    
    
    form.addEventListener("submit", function(event) {
        if (!validCaptcha) {
            event.preventDefault();
            document.getElementById("captcha-response").textContent = "❌ Veuillez valider le captcha avant de soumettre le formulaire.";
            document.getElementById("captcha-response").className = "mt-2 text-sm text-red-600 dark:text-red-400";
        }
    });
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
