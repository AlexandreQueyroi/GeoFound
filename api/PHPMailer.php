<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(0);
ini_set('display_errors', 0);

$data = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);

error_log("PHPMailer - Données reçues: " . json_encode($data));

if ($data === null) {
    http_response_code(400);
    echo json_encode([
        'state' => 'failed',
        'error' => 'Données invalides'
    ]);
    exit;
}

$type = $data['type'] ?? '';
$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$id = $data['id'] ?? '';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alexandregeofound@gmail.com';
    $mail->Password = 'vtaakhznwkorpknn';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;

    $mail->setFrom('alexandregeofound@gmail.com', 'Geofound Contact');

    if ($type === "notifyUnreadMessage") {
        $sender = $data['sender_pseudo'] ?? '';
        $receiver = $data['receiver_pseudo'] ?? '';
        $receiver_email = $data['receiver_email'] ?? '';
        $content = $data['content'] ?? '';
        $link = $data['link'] ?? '';

        if (empty($receiver_email)) {
            throw new Exception("L'adresse email du destinataire est manquante");
        }

        $mail->addAddress($receiver_email, $receiver);
        $mail->Subject = "Nouveau message non lu sur GeoFound";
        $mail->Body = "<div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;'><div style='max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'><div style='background: #081225; color: white; padding: 20px; text-align: center;'><h1 style='margin: 0;'>Vous avez un nouveau message non lu !</h1></div><div style='padding: 20px;'><p>Bonjour <strong>$receiver</strong>,</p><p>Vous avez reçu un message de <strong>$sender</strong> :</p><blockquote style='background:#f1f1f1;padding:10px;border-radius:5px;'>$content</blockquote><p><a href='$link' style='background: #081225; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Voir la conversation</a></p><p>À bientôt,<br>L'équipe de GeoFound</p></div><div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #555;'>© 2025 GeoFound. Tous droits réservés.</div></div></div>";
        $mail->AltBody = "Bonjour $receiver,\n\nVous avez reçu un message de $sender :\n\n$content\n\nVoir la conversation : $link\n\nL'équipe de GeoFound.";
    } elseif ($type === "verifyEmail") {
        $email = $data['email'] ?? '';
        $username = $data['username'] ?? '';
        $token = $data['token'] ?? '';
        if (empty($email) || empty($token)) {
            throw new Exception("Email ou token manquant pour la vérification");
        }
        $mail->addAddress($email, $username);
        $mail->Subject = "Vérification de votre nouvelle adresse email";
        $verificationLink = 'https://geofound.fr/api/verify_email.php?token=' . urlencode($token);
        $mail->Body = "<div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;'><div style='max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'><div style='background: #081225; color: white; padding: 20px; text-align: center;'><h1 style='margin: 0;'>Vérification de votre nouvelle adresse email</h1></div><div style='padding: 20px;'><p>Bonjour <strong>$username</strong>,</p><p>Merci de vouloir changer votre adresse email. Veuillez confirmer ce changement en cliquant sur le bouton ci-dessous :</p><div style='text-align: center; margin: 30px 0;'><a href='$verificationLink' style='background: #081225; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Confirmer mon adresse email</a></div><p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce mail.</p><p>À bientôt,<br>L'équipe de Geofound</p></div><div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #555;'>© 2025 GeoFound. Tous droits réservés.</div></div></div>";
        $mail->AltBody = "Bonjour $username,\n\nMerci de vouloir changer votre adresse email. Veuillez confirmer ce changement en visitant le lien suivant : $verificationLink\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez ce mail.\n\nL'équipe de Geofound.";
    } else {
        http_response_code(400);
        echo json_encode([
            'state' => 'failed',
            'error' => 'Type de notification non supporté'
        ]);
        exit;
    }

    $mail->isHTML(true);
    $mail->send();
    
    echo json_encode([
        'state' => 'success',
        'data' => $data
    ]);
} catch (Exception $e) {
    error_log("Erreur PHPMailer : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'state' => 'failed',
        'error' => $e->getMessage(),
        'details' => [
            'username' => $username,
            'type' => $type,
            'id' => $id,
            'email' => $email
        ]
    ]);
}
?>