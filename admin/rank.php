<?php
include_once '../build/header_back.php';
include_once '../api/bdd.php';
// if (!($_SESSION['user_rank'] === "admin" || $_SESSION['user_rank'] === "mod")) {
//     header("Location: /index.php");
//     exit();
// }
?>

<!-- User Name || User Rank || User Email || IsActivated || Avatar || isLoggedIn -->

<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold text-white mb-4 text-center">Gestion des Rôles</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg mx-auto">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="p-4 text-center">Rôle</th>
                    <th class="p-4 text-center">Couleur</th>
                    <th class="p-4 text-center">Permissions</th>
                    <th class="p-4 text-center">Éditer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT name, color, id FROM ranks";
                $result = $conn->query($sql);
                include "../api/rank.php";

                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr class='border-b border-gray-300'>";
                        echo "<th class='text-center px-4'>" . $row['name'] . "</th>";
                        echo "<td class='text-center px-4'>" . getRankColorDiv($row['id']) . "</td>";
                        echo "<td class='text-center px-4'> PERMS </td>";
                        echo "<td class='text-center px-4'><div class='flex justify-center items-center'><a href='edit_rank.php?id=" . $row['id'] . "' class='text-blue-500 hover:underline'><span class='iconify' data-icon='tabler:settings'></span></a></div></td>";
                        echo "</tr>";
                    }
                }
                ?>
                <tr>
                    <td>
                        <div class="flex justify-center items-center">
                            <button data-modal-target="modal-addgrade" data-modal-toggle="modal-addgrade"
                                class="text-white bg-blue-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                type="button">Ajouter un grade</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- <div class="bg-gray-900 p-6 rounded-lg shadow-lg max-w-screen-xl mx-auto mt-10">
</div> -->

<?php
include_once '../build/footer.php';