<?php
session_start();
$_SESSION['last_url'] = $_SERVER['REQUEST_URI'];
if (!isset($_SESSION['user'])) {
    header('Location: /action/userConnection.php');
    exit();
}
include_once(__DIR__ . '/../build/header_back.php');
include_once(__DIR__ . '/../api/bdd.php');
// if (!($_SESSION['user_rank'] === "admin" || $_SESSION['user_rank'] === "mod")) {
//     header("Location: /index.php");
//     exit();
// }
?>

<!-- User Name || User Rank || User Email || IsActivated || Avatar || isLoggedIn -->

<div class="bg-gray-900 p-6 rounded-lg shadow-lg max-w-screen-xl mx-auto mt-10">
    <h2 class="text-2xl font-bold text-white mb-4">Gestion des utilisateurs</h2>
    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="p-4 text-left">Nom d'utilisateur</th>
                <th class="p-4 text-left">Rang</th>
                <th class="p-4 text-left">Email</th>
                <th class="p-4 text-left">Activé</th>
                <th class="p-4 text-left">Avatar</th>
                <th class="p-4 text-left">Connecté</th>
                <th class="p-4 text-left">Editer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT rank, pseudo, email, desactivated, avatar, token, id FROM users";
            $result = $conn->query($sql);
            include "../api/rank.php";

            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr class='border-b border-gray-300'>";
                    echo "<th class='text-center px-4'>" . $row['pseudo'] . "</th>";
                    echo "<td class='text-center px-4'>" . getRankColorSpan($row['rank']) . "</td>";
                    // echo "<td class='text-center px-4'>" . $row['email'] . "</td>";
                    echo "<td class='text-center px-4'> exemple@exemple.com </td>";
                    echo "<td class='text-center px-4'><div class='flex justify-center items-center'><span class=\"iconify text-lg text-bold " . ($row['desactivated'] ? "text-red-500" : "text-green-500") . "\" data-icon='" . ($row['desactivated'] ? "tabler:x" : "tabler:check") . "'></span></div></td>";
                    echo "<td class='text-center px-4'><div class='flex justify-center items-center'><img src='data:image/jpeg;base64," . $row['avatar'] . "' alt='Avatar' class='w-8 h-8 rounded-full'></div></td>";
                    echo "<td class='text-center px-4'><div class='flex justify-center items-center'><span class=\"iconify text-lg text-bold " . ($row['token'] ? "text-green-500" : "text-red-500") . "\" data-icon='" . ($row['token'] ? "tabler:check" : "tabler:x") . "'></span></div></td>";
                    echo "<td class='text-center px-4'><div class='flex justify-center items-center'><a href='edit_user.php?id=" . $row['id'] . "' class='text-blue-500 hover:underline'><span class='iconify' data-icon='tabler:settings'></span></a></div></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <button data-modal-target="modal-addgrade" data-modal-toggle="modal-addgrade"
        class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        type="button">Ajouter un grade (temp)</button>
</div>

<?php
include_once(__DIR__ . '/../build/footer.php');