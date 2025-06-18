<?php
include_once(__DIR__ . '/../build/header.php');
include_once(__DIR__ . '/../api/bdd.php');
if (!isset($_SESSION['id'])) {
    header('Location: /');
    exit;
}

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT pseudo, email, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Paramètres du compte</h1>
        
        <form id="editProfileForm" class="space-y-6 bg-[#081225] p-6 rounded-lg">
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-white">Avatar</h2>
                <div class="flex items-center space-x-4">
                    <div id="avatarPreview" class="w-24 h-24 rounded-full overflow-hidden">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="data:image/jpeg;base64,<?= $user['avatar'] ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-300"></div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="openAvatarEditor" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Éditer l'avatar
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <label for="pseudo" class="block text-white">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($user['pseudo']) ?>" 
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-white">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-white">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" 
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="space-y-2">
                <label for="confirmPassword" class="block text-white">Confirmer le mot de passe</label>
                <input type="password" id="confirmPassword" name="confirmPassword" 
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        <div class="mt-8 space-y-4">
            <h2 class="text-xl font-semibold text-white mb-4">Actions du compte</h2>
            
            <div class="flex space-x-4">
                <button id="exportPDF" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Exporter en PDF
                </button>
                <button id="exportJSON" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Exporter en JSON
                </button>
            </div>

            <div class="flex space-x-4">
                <button id="deactivateAccount" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                    Désactiver le compte
                </button>
                <button id="deleteAccount" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Supprimer le compte
                </button>
            </div>
        </div>
    </div>
</div>

<div id="avatarEditorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-[#081225] p-6 rounded-lg max-w-2xl w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-white">Éditeur d'avatar</h3>
            <button id="closeAvatarEditor" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-4">
                <div>
                    <label class="block text-white mb-2">Cheveux</label>
                    <select id="hairStyle" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2">
                        <option value="style1">Style 1</option>
                        <option value="style2">Style 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-white mb-2">Visage</label>
                    <select id="faceStyle" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2">
                        <option value="face1">Visage 1</option>
                        <option value="face2">Visage 2</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center justify-center">
                <div id="avatarPreview" class="w-48 h-48 rounded-full bg-gray-300"></div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-4">
            <button id="saveAvatar" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Enregistrer
            </button>
            <button id="cancelAvatar" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Annuler
            </button>
        </div>
    </div>
</div>

<div id="deactivateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-[#081225] p-6 rounded-lg max-w-md w-full">
        <h3 class="text-xl font-semibold text-white mb-4">Désactiver le compte</h3>
        <p class="text-gray-300 mb-6">Êtes-vous sûr de vouloir désactiver votre compte ? Vos posts ne seront plus visibles.</p>
        <div class="flex justify-end space-x-4">
            <button id="confirmDeactivate" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                Confirmer
            </button>
            <button id="cancelDeactivate" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Annuler
            </button>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-[#081225] p-6 rounded-lg max-w-md w-full">
        <h3 class="text-xl font-semibold text-white mb-4">Supprimer le compte</h3>
        <p class="text-gray-300 mb-6">Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
        <div class="flex justify-end space-x-4">
            <button id="confirmDelete" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Confirmer
            </button>
            <button id="cancelDelete" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Annuler
            </button>
        </div>
    </div>
</div>

<script src="/me/edit.js"></script>

<?php
include_once(__DIR__ . '/../build/footer.php');
?>
