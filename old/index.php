<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once(__DIR__ . '/build/header.php');
include_once(__DIR__ . '/api/bdd.php');
include_once(__DIR__ . '/modal.php');

if (isset($_POST['envoyer'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vous devez être connecté pour créer un post');</script>";
    } else {
        $description = $_POST['description'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_data = file_get_contents($image_tmp);
        $image_base64 = base64_encode($image_data);
        
        try {
            $sql = "INSERT INTO post_content (content) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$image_base64]);
            $image_id = $conn->lastInsertId();
            
            $sql = "INSERT INTO post (description, content_id, user_id, date) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$description, $image_id, $_SESSION['user_id']]);
            
            unset($_POST['envoyer'], $_POST['description']);
            echo "<script>window.location.href = 'index.php';</script>";
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>

<div class="flex-grow max-w-4xl mx-auto px-4 py-8">
    <div class="text-black">
        <div id="addPostModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Créer un nouveau post</h2>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form name="addpost" method="post" action="" enctype="multipart/form-data" class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <div id="imagePreview" class="hidden mb-4">
                                    <img id="preview" src="" alt="Aperçu" class="mx-auto max-h-48 rounded-lg">
                                </div>
                                <svg id="uploadIcon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500">
                                        <span>Télécharger une image</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*" required onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 10MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Décrivez votre post..." required></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Annuler
                        </button>
                        <button type="submit" name="envoyer" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Publier
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <button onclick="openModal()" class="bg-[
            <span class="iconify text-4xl" data-icon="tabler:plus"></span>
        </button>

        <?php
        $sql = "SELECT post.id, post.description, post_content.content, post.user_id, post.latitude, post.longitude, post.name, post.date 
                FROM post 
                INNER JOIN post_content ON post.content_id = post_content.id 
                ORDER BY post.id DESC";
        try {
            if (!$conn) {
                die("Erreur de connexion à la base de données");
            }

            $result = $conn->query($sql);
            
            if ($result === false) {
                die("Erreur lors de l'exécution de la requête SQL");
            }
            
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $user_name = "Unknown";
                    $user = $row['user_id'];
                    $sql = "SELECT pseudo FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$user]);
                    $resT = $stmt->fetch();
                    if ($resT) {
                        $user_name = $resT["pseudo"];
                    }

                    $user_reaction = null;
                    if (isset($_SESSION['user_id'])) {
                        $stmt = $conn->prepare("SELECT state FROM reaction WHERE post_id = ? AND user_id = ?");
                        $stmt->execute([$row['id'], $_SESSION['user_id']]);
                        $user_reaction = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                    $stmt = $conn->prepare("SELECT state, COUNT(*) as count FROM reaction WHERE post_id = ? GROUP BY state");
                    $stmt->execute([$row['id']]);
                    $reactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $reaction_counts = [];
                    foreach ($reactions as $reaction) {
                        $reaction_counts[$reaction['state']] = $reaction['count'];
                    }

                    $description = htmlspecialchars($row['description']);
                    $image_base64 = $row['content'];
                    echo '<div class="bg-white max-w-lg mx-auto p-4 rounded-lg shadow-lg mb-4" id="post">';
                    echo '<div id="postProfil" class="flex items-left p-4 gap-x-2">';
                    echo '<img src="img/examplePostProfil.jpg" alt="Image" class="h-8 w-8 object-contain rounded-full aspect-square">';
                    echo "<h3>$user_name</h3>";
                    echo '</div>';
                    echo '<div>';
                    echo '<img id="postPicture" src="data:image/jpeg;base64,' . $image_base64 . '" alt="Image" class="w-auto h-max-60 object-contain">';
                    echo '<div id="postReaction" class="flex justify-between p-4 bg-white">';
                    echo '<div class="flex items-center gap-4">';
                    
                    $heart_color = ($user_reaction && $user_reaction['state'] === 'like') ? 'text-red-500' : 'text-gray-400';
                    echo '<button onclick="react(' . $row['id'] . ', \'like\')" class="flex items-center gap-1">';
                    echo '<svg class="w-6 h-6 ' . $heart_color . '" fill="' . ($user_reaction && $user_reaction['state'] === 'like' ? 'currentColor' : 'none') . '" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">';
                    echo '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />';
                    echo '</svg>';
                    if (isset($reaction_counts['like'])) {
                        echo '<span class="text-gray-400">' . $reaction_counts['like'] . '</span>';
                    }
                    echo '</button>';

                    echo '<button onclick="showComments(' . $row['id'] . ')" class="flex items-center gap-1">';
                    echo '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">';
                    echo '<path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />';
                    echo '</svg>';
                    if (isset($comment_counts[$row['id']])) {
                        echo '<span class="text-gray-400">' . $comment_counts[$row['id']] . '</span>';
                    }
                    echo '</button>';

                    echo '<button onclick="copyPostLink(' . $row['id'] . ')" class="flex items-center gap-1">';
                    echo '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">';
                    echo '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />';
                    echo '</svg>';
                    echo '</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="p-4">';
                    echo '<p class="text-sm text-gray-600">' . $description . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="text-center text-gray-500">Aucun post trouvé</div>';
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
    </div>
</div>

<script>
function sharePost(postId) {
    const url = `${window.location.origin}/post.php?id=${postId}`;
    if (navigator.share) {
        navigator.share({
            title: 'GeoFound - Post',
            url: url
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('Lien copié dans le presse-papiers !');
        }).catch(err => {
            console.error('Erreur lors de la copie :', err);
        });
    }
}

function react(postId, state) {
    fetch('api/react.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&state=${state}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            location.reload();
        }
    });
}

function showComments(postId) {
    window.location.href = 'post.php?id=' + postId;
}

function copyPostLink(postId) {
    const url = window.location.origin + '/post.php?id=' + postId;
    navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié !');
    }).catch(err => {
        console.error('Erreur lors de la copie du lien:', err);
    });
}

function openModal() {
    document.getElementById('addPostModal').classList.remove('hidden');
    document.getElementById('addPostModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('addPostModal').classList.add('hidden');
    document.getElementById('addPostModal').classList.remove('flex');
    document.querySelector('form[name="addpost"]').reset();
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('uploadIcon').classList.remove('hidden');
    document.getElementById('preview').src = '';
}

document.getElementById('addPostModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

const dropZone = document.querySelector('.border-dashed');
const fileInput = document.querySelector('input[type="file"]');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-teal-500');
}

function unhighlight(e) {
    dropZone.classList.remove('border-teal-500');
}

dropZone.addEventListener('drop', handleDrop, false);

function previewImage(input) {
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreview');
    const uploadIcon = document.getElementById('uploadIcon');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            imagePreview.classList.remove('hidden');
            uploadIcon.classList.add('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    const fileInput = document.querySelector('input[type="file"]');
    fileInput.files = files;
    previewImage(fileInput);
}
</script>

<?php
include_once(__DIR__ . '/build/footer.php');
?>