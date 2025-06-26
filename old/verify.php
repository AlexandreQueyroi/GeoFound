<?php
include_once(__DIR__ . '/build/header.php');
include_once(__DIR__ . '/api/bdd.php');

echo '<div class="flex items-center justify-center min-h-screen">';
echo '<div class="p-6 bg-white rounded-lg shadow-md text-center">';

if (isset($_GET['verificationID'])) {
    $encodedId = $_GET['verificationID'];
    $decodedId = base64_decode($encodedId);
    if ($decodedId === false || strpos($decodedId, '-') === false) {
        $status = 'error';
    } else {
        list($user, $id, $time) = explode('-', $decodedId, 3);
        if (is_numeric($user) || !is_numeric($time) || !is_numeric($id)) {
            $status = 'error';
        } else {
            $sql = "SELECT verified FROM users WHERE pseudo = :pseudo AND id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pseudo', $user, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                if ($result['verified'] == 1) {
                    $status = 'already_verified';
                } else {
                    try {
                        $sql = "UPDATE users SET verified = 1, verified_at = :time WHERE pseudo = :pseudo AND id = :id";
                        $stmt = $conn->prepare($sql);
                        $date = date("Y-m-d H:i:s");
                        $stmt->bindParam(':time', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':pseudo', $user, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $status = 'success';
                    } catch (PDOException $e) {
                        echo "<p class='text-red-600 font-semibold text-lg'>Erreur de vérification, veuillez contacter le support en leur indiquant le code : \"" . $_GET['verificationID'] . "\"</p>";
                    }
                }
            } else {
                $status = 'error';
            };

        }
    }
} else {
    $status = 'error';
}


if ($status == 'success') {
    echo '<p class="text-green-600 font-semibold text-lg">Vérification effectuée, vous allez être connecté et redirigé dans quelques instant...</p>';
} else if ($status == 'already_verified') {
    echo '<p class="text-yellow-600 font-semibold text-lg">Votre compte est déjà vérifié !</p>';
} else if ($status == 'error') {
    echo '<p class="text-red-600 font-semibold text-lg">Erreur de vérification, veuillez contacter le support en leur indiquant le code : "' . $_GET['verificationID'] . '"</p>';
    custom_log("$status", "Verification ERROR : " . ($_GET['verificationID'] ?? 'undefined'), "verify.php");

} else {
    echo '<p class="text-red-600 font-semibold text-lg">Erreur de vérification, veuillez contacter le support en leur indiquant le code : "' . $_GET['verificationID'] . '"</p>';
}

echo '</div>';
echo '</div>';


include_once(__DIR__ . '/build/footer.php');