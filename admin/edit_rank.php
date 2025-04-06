<?php
include_once '../build/header_back.php';
include_once '../api/bdd.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT color, name FROM ranks WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $rank = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "Aucun rôle avec l'ID " . $_GET['id'] . ".";
}
?>
<div class="flex items-center justify-center">
    <form method="POST" action="../action/editRank.php?id=<?php echo $id; ?>" enctype="multipart/form-data"
        class="max-w-md mx-auto my-5 p-5 border border-gray-300 rounded-lg bg-gray-100 shadow-md">
        <label for="grade-name" class="block mb-2 font-bold">Nom du rôle:</label>
        <input type="text" id="grade-name" name="grade-name" value="<?php echo htmlspecialchars($rank['name']); ?>"
            required class="w-full p-2 mb-4 border border-gray-300 rounded-md">
        <label for="grade-color" class="block mb-2 font-bold">Couleur du rôle:</label>
        <input type="color" id="grade-color" name="grade-color" value="#<?php echo htmlspecialchars($rank['color']); ?>"
            required class="w-full p-2 mb-4 border border-gray-300 rounded-md">
        <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md text-lg hover:bg-blue-700">Modifier le
            grade</button>
    </form>
</div>



<?php
include_once '../build/footer.php';