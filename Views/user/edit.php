<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/modal.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-white mb-8">Édition du profil</h1>
        <div class="mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <div>
                    <canvas id="avatarCanvas" width="300" height="300" class="border rounded mb-3 bg-gray-800"></canvas>
                    <div class="flex justify-center">
                        <button id="saveAvatarBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            Enregistrer mon avatar
                        </button>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label for="skinSelect" class="block text-white mb-1">Teint</label>
                        <select id="skinSelect" class="w-full rounded px-2 py-1">
                            <option value="Skin/tint1_head.png">Teint 1</option>
                            <option value="Skin/tint2_head.png">Teint 2</option>
                            <option value="Skin/tint3_head.png">Teint 3</option>
                            <option value="Skin/tint4_head.png">Teint 4</option>
                            <option value="Skin/tint5_head.png">Teint 5</option>
                            <option value="Skin/tint6_head.png">Teint 6</option>
                            <option value="Skin/tint7_head.png">Teint 7</option>
                            <option value="Skin/tint8_head.png">Teint 8</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="headSelect" class="block text-white mb-1">Visage</label>
                        <select id="headSelect" class="w-full rounded px-2 py-1">
                            <option value="Face/face1.png">Visage 1</option>
                            <option value="Face/face2.png">Visage 2</option>
                            <option value="Face/face3.png">Visage 3</option>
                            <option value="Face/face4.png">Visage 4</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="hairSelect" class="block text-white mb-1">Coiffure</label>
                        <select id="hairSelect" class="w-full rounded px-2 py-1">
                            <optgroup label="Noir">
                                <option value="Hair/Black/Man1B.png">Homme noir 1</option>
                                <option value="Hair/Black/Man2B.png">Homme noir 2</option>
                                <option value="Hair/Black/Man3B.png">Homme noir 3</option>
                                <option value="Hair/Black/Woman1B.png">Femme noire 1</option>
                                <option value="Hair/Black/Woman2B.png">Femme noire 2</option>
                                <option value="Hair/Black/Woman3B.png">Femme noire 3</option>
                            </optgroup>
                            <optgroup label="Blond">
                                <option value="Hair/Blonde/Man1Bl.png">Homme blond 1</option>
                                <option value="Hair/Blonde/Man2Bl.png">Homme blond 2</option>
                                <option value="Hair/Blonde/Man3Bl.png">Homme blond 3</option>
                                <option value="Hair/Blonde/Woman1Bl.png">Femme blonde 1</option>
                                <option value="Hair/Blonde/Woman2Bl.png">Femme blonde 2</option>
                                <option value="Hair/Blonde/Woman3Bl.png">Femme blonde 3</option>
                            </optgroup>
                            <optgroup label="Brun">
                                <option value="Hair/Brown/Man1Br.png">Homme brun 1</option>
                                <option value="Hair/Brown/Man2Br.png">Homme brun 2</option>
                                <option value="Hair/Brown/Man3Br.png">Homme brun 3</option>
                                <option value="Hair/Brown/Woman1Br.png">Femme brune 1</option>
                                <option value="Hair/Brown/Woman2Br.png">Femme brune 2</option>
                                <option value="Hair/Brown/Woman3Br.png">Femme brune 3</option>
                            </optgroup>
                            <optgroup label="Roux">
                                <option value="Hair/Red/Man1R.png">Homme roux 1</option>
                                <option value="Hair/Red/Man2R.png">Homme roux 2</option>
                                <option value="Hair/Red/Man3R.png">Homme roux 3</option>
                                <option value="Hair/Red/Woman1R.png">Femme rousse 1</option>
                                <option value="Hair/Red/Woman2R.png">Femme rousse 2</option>
                                <option value="Hair/Red/Woman3R.png">Femme rousse 3</option>
                            </optgroup>
                            <optgroup label="Blanc">
                                <option value="Hair/White/Man1W.png">Homme blanc 1</option>
                                <option value="Hair/White/Man2W.png">Homme blanc 2</option>
                                <option value="Hair/White/Man3W.png">Homme blanc 3</option>
                                <option value="Hair/White/Woman1W.png">Femme blanche 1</option>
                                <option value="Hair/White/Woman2W.png">Femme blanche 2</option>
                                <option value="Hair/White/Woman3W.png">Femme blanche 3</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <form id="editProfileForm" class="space-y-6" method="post" enctype="multipart/form-data" action="/api/update_profile.php">
            <div class="space-y-2">
                <label for="pseudo" class="block text-white">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($user['pseudo'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="space-y-2">
                <label for="email" class="block text-white">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-white">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="space-y-2">
                <label for="confirmPassword" class="block text-white">Confirmer le mot de passe</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<script src="/assets/js/avatar-customizer.js"></script>
<script src="/assets/js/editProfile.js"></script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 