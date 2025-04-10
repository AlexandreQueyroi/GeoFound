<?php
session_start();
if (!($_SESSION['rank'] === "admin" || $_SESSION['rank'] === "mod")) {
    header("Location: /index.php");
    exit();
}
include_once(__DIR__ . '/../api/bdd.php');
include_once(__DIR__ . '/../build/header_back.php');
?>

<div class="bg-gray-900">

    <div class="max-w-screen-xl mx-auto p-6">
        <div class="bg-gray-300 p-4 rounded-xl flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <label for="start" class="text-sm font-semibold">Début</label>
                <input type="date" id="start" class="p-2 rounded-lg border border-gray-400">
            </div>
            <div class="flex items-center gap-2">
                <label for="end" class="text-sm font-semibold">Fin</label>
                <input type="date" id="end" class="p-2 rounded-lg border border-gray-400">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold">Période de temps</label>
                <select class="p-2 rounded-lg border border-gray-400">
                    <option>Mois</option>
                    <option>Année</option>
                    <option>6 mois</option>
                    <option>2 semaines</option>
                    <option>1 jour</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="bg-gray-300 text-black p-6 rounded-xl text-center">
                <p class="text-lg font-semibold">En ligne</p>
                <p class="text-4xl font-bold">35678</p>
            </div>
            <div class="bg-gray-300 text-black p-6 rounded-xl text-center">
                <p class="text-lg font-semibold">Publication par</p>
                <select class="p-1 rounded-lg border border-gray-400 text-sm">
                    <option>jour</option>
                    <option>semaine</option>
                </select>
                <p class="text-4xl font-bold">3020</p>
            </div>
            <div class="bg-gray-300 text-black p-6 rounded-xl text-center">
                <p class="text-lg font-semibold">Nombre de j'aime par</p>
                <select class="p-1 rounded-lg border border-gray-400 text-sm">
                    <option>jour</option>
                    <option>semaine</option>
                </select>
                <p class="text-4xl font-bold">12334</p>
            </div>
        </div>

        <div class="bg-gray-300 text-black p-6 rounded-xl mt-6">
            <p class="text-lg font-semibold">Interaction</p>
            <div class="h-48 bg-gray-200 rounded-lg flex items-end p-4">
                <div class="w-1/6 bg-gray-500 h-16 mx-1"></div>
                <div class="w-1/6 bg-gray-500 h-24 mx-1"></div>
                <div class="w-1/6 bg-gray-500 h-12 mx-1"></div>
                <div class="w-1/6 bg-gray-500 h-32 mx-1"></div>
                <div class="w-1/6 bg-gray-500 h-40 mx-1"></div>
                <div class="w-1/6 bg-gray-500 h-28 mx-1"></div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="bg-gray-300 text-black p-6 rounded-xl text-center">
                <p class="text-lg font-semibold">Bannissement</p>
                <p class="text-4xl font-bold">79</p>
            </div>
            <div class="bg-gray-300 text-black p-6 rounded-xl">
                <p class="text-lg font-semibold">Signalement</p>
                <div class="mt-2">
                    <p class="flex justify-between"><span>Comptes</span> <span>1352</span></p>
                    <p class="flex justify-between"><span>Publications</span> <span>671</span></p>
                    <p class="flex justify-between"><span>Commentaires</span> <span>9088</span></p>
                </div>
            </div>
            <div class="bg-gray-300 text-black p-6 rounded-xl">
                <p class="text-lg font-semibold">Localisation</p>
                <div class="mt-2">
                    <p class="flex justify-between"><span>France</span> <span>16980</span></p>
                    <p class="flex justify-between"><span>USA</span> <span>15897</span></p>
                    <p class="flex justify-between"><span>Royaume-Uni</span> <span>14013</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once(__DIR__ . '/../build/footer.php');
