<?php
include_once './build/header.php';
include_once './api/bdd.php';
include_once './modal.php';

if (isset($_POST['envoyer'])) {
    $description = $_POST['description'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_data = file_get_contents($image_tmp);
    $image_base64 = base64_encode($image_data);
    $sql = "INSERT INTO post_content (content) VALUES (?)";
    $stmg = $conn->prepare($sql);
    $stmg->bind_param("s", $image_base64);
    $stmg->execute();
    $image_id = $conn->lastInsertId();
    $stmg->close();
    $sql = "INSERT INTO post (description, content_id, user_id) VALUES (?, ?, 1)";
    $stmg = $conn->prepare($sql);
    $stmg->bind_param("si", $description, $image_id);
    $stmg->execute();
    $stmg->close();
    unset($_POST['envoyer'], $_POST['description']);
    header("Location: index.php");
    exit();
};
?>

<div class="text-black">

    <button class="bg-[#00272B] text-white p-4 rounded-full fixed bottom-32 right-5">
        <span class="iconify text-4xl" data-icon="tabler:plus"></span>
    </button>

    <?php
    $sql = "SELECT post.description, post_content.content, post.user_id FROM post 
        INNER JOIN post_content ON post.content_id = post_content.id 
        ORDER BY post.id DESC";
    $result = $conn->query($sql);

    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user_name = "Unknown";
            $user_id = $row['user_id'];
            $sql = "SELECT pseudo FROM users WHERE id = ?";
            $stmg = $conn->prepare($sql);
            $stmg->execute([$user_id]);
            $resT = $stmg->fetch();
            if ($resT) {
                $user_name = $resT["pseudo"];
            }
            $description = htmlspecialchars($row['description']);
            $image_base64 = $row['content'];
            echo '<div class="bg-white max-w-lg mx-auto p-4 rounded-lg shadow-lg" id="post">';
            echo '<div id="postProfil" class="flex items-left p-4 gap-x-2">';
            echo '<img src="img/examplePostProfil.jpg" alt="Image" class="h-8 w-8 object-contain rounded-full aspect-square">';
            echo "<h3>$user_name</h3>";
            echo '</div>';
            echo '<div>';
            echo '<img id="postPicture" src="data:image/jpeg;base64,' . $image_base64 . '" alt="Image" class="w-auto h-max-60 object-contain">';
            echo '<div id="postReaction" class="flex justify-between p-4 bg-white">';
            echo '<div class="flex item-left gap-4">';
            echo '<span class="iconify text-2xl text-red-500" data-icon="tabler:heart-filled"></span>';
            echo '<span class="iconify text-2xl hover:text-gray-800" data-icon="tabler:message-circle-filled"></span>';
            echo '<span class="iconify text-2xl hover:text-gray-800" data-icon="tabler:send"></span>';
            echo '</div>';
            echo '<div class="flex item-right gap-4">';
            echo '<span class="iconify text-2xl text-yellow-500" data-icon="tabler:bookmark-filled"></span>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div id="postDescription" class="bg-white p-4">';
            echo '<div class="flex flex-col">';
            echo "<h4>$user_name</h4>";
            echo '<p>' . $description . '</p>';
            echo '</div>';
            echo '<div class="text-gray-800 flex">';
            echo '<span class="iconify text-2xl" data-icon="tabler:map-pin"></span>';
            echo '<p>Localisation</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="h-4"></div>';
        }
    } else {
        echo '<p class="text-center">Aucun post disponible.</p>';
    }
    ?>
    <div class="bg-white text-gray-800 p-4 rounded-lg">
        <h4 class="text-red-500 text-center font-bold">Formulaire temporaire</h4>
        <form name="addpost" method="post" action="" enctype="multipart/form-data">
            <input type="file" name="image" /><br>
            <textarea name="description" placeholder="Description"></textarea><br>
            <input type="submit" name="envoyer" value="Envoyer" />
        </form>
    </div>
</div>




<?php
include_once './build/footer.php';