<?php
$body = file_get_contents('php://input');

$data = json_decode($body, true);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$username = isset($data['username']) ? $data['username'] : '';
$id = isset($data['id']) ? $data['id'] : '';
$type = isset($data['type']) ? $data['type'] : '';
$email = isset($data['email']) ? $data['email'] : '';


$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alexandregeofound@gmail.com';
    $mail->Password = 'vtaakhznwkorpknn';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('alexandregeofound@gmail.com', 'Geofound Contact');
    $mail->addAddress($email, $username);

    $mail->isHTML(true);
    if ($type === "confirmCreate") {
        $id = base64_encode($username . "-" . $id . "-" . time());


        $mail->Subject = "Bienvenue $username sur notre site !";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;'>
          <div style='max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
            <div style='background: #081225; color: white; padding: 20px; text-align: center;'>
              <h1 style='margin: 0;'>Bienvenue $username !</h1>
            </div>
            <div style='padding: 20px;'>
              <p>Bonjour <strong>$username</strong>,</p>
              <p>Merci de vous être inscrit sur notre site. Veuillez confirmer votre compte en cliquant sur le bouton ci-dessous :</p>
              <div style='text-align: center; margin: 30px 0;'>
                <a href='https://geofound.fr/verify?verificationID=" . urlencode($id) . "' style='background: #081225; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Confirmer mon compte</a>
              </div>
              <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce mail.</p>
              <p>À bientôt,<br>L'équipe de Geofound</p>
            </div>
            <div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #555;'>
              © 2025 Geofound. Tous droits réservés.
            </div>
          </div>
        </div>";

        $mail->AltBody = "Bonjour $username,\n\nMerci de vous être inscrit sur notre site. Veuillez confirmer votre compte en visitant le lien suivant : https://geofound.fr/verify?verificationID=" . urlencode($id) . "\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez ce mail.\n\nL'équipe de Geofound.";
        $state = "success";
        $data = array(
            'username' => $username,
            'type' => 'confirmCreate',
            'email' => $email
        );
    } elseif ($type === "newsletter") {
        $messages = [
            "<h2 style='color: #081225;'>Découvrez nos nouvelles fonctionnalités !</h2><p>Nous avons ajouté des outils incroyables pour améliorer votre expérience. Connectez-vous dès maintenant pour les tester !</p>",
            "<h2 style='color: #081225;'>Une surprise vous attend !</h2><p>Rendez-vous sur votre espace membre et découvrez une surprise exclusive réservée à nos abonnés les plus fidèles !</p>",
            "<h2 style='color: #081225;'>Nos derniers articles !</h2><p>Découvrez les 5 posts les plus liké ce mois-ci et restez à jour sur les dernières photos du monde.</p>"
        ];
        $randomMessage = $messages[array_rand($messages)];

        $mail->Subject = "La newsletter du mois - $username";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;'>
          <div style='max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
            <div style='background: #081225; color: white; padding: 20px; text-align: center;'>
              <h1 style='margin: 0;'>Bonjour $username !</h1>
            </div>
            <div style='padding: 20px;'>
              $randomMessage
              <div style='text-align: center; margin: 30px 0;'>
                <a href='https://geofound.fr/newsletter' style='background: #081225; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>En savoir plus</a>
              </div>
              <p>Merci de votre fidélité.<br>L'équipe de Geofound</p>
            </div>
            <div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #555;'>
              © 2025 Geofound. Vous recevez cet email car vous êtes abonné à notre newsletter.
            </div>
          </div>
        </div>";

        $mail->AltBody = "Bonjour $username,\n\nVoici votre newsletter du mois ! Connectez-vous pour découvrir toutes nos nouveautés.\n\nhttps://geofound.fr/newsletter\n\nMerci de votre fidélité, L'équipe de Geofound.";
        $state = "success";
        $data = array(
            'username' => $username,
            'type' => 'newsletter',
            'email' => $email
        );
    } elseif ($type === "notifyUnreadMessage") {
        $sender = isset($data['sender_pseudo']) ? $data['sender_pseudo'] : '';
        $receiver = isset($data['receiver_pseudo']) ? $data['receiver_pseudo'] : '';
        $receiver_email = isset($data['receiver_email']) ? $data['receiver_email'] : '';
        $content = isset($data['content']) ? $data['content'] : '';
        $link = isset($data['link']) ? $data['link'] : '';
        $mail->addAddress($receiver_email, $receiver);
        $mail->Subject = "Nouveau message non lu sur GeoFound";
        $mail->Body = "<div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;'><div style='max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'><div style='background: #081225; color: white; padding: 20px; text-align: center;'><h1 style='margin: 0;'>Vous avez un nouveau message non lu !</h1></div><div style='padding: 20px;'><p>Bonjour <strong>$receiver</strong>,</p><p>Vous avez reçu un message de <strong>$sender</strong> :</p><blockquote style='background:#f1f1f1;padding:10px;border-radius:5px;'>$content</blockquote><p><a href='$link' style='background: #081225; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Voir la conversation</a></p><p>À bientôt,<br>L'équipe de GeoFound</p></div><div style='background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #555;'>© 2025 GeoFound. Tous droits réservés.</div></div></div>";
        $mail->AltBody = "Bonjour $receiver,\n\nVous avez reçu un message de $sender :\n\n$content\n\nVoir la conversation : $link\n\nL'équipe de GeoFound.";
        $state = "success";
    } else {
        http_response_code(400);
        echo "Type inconnu.";
        exit;
    }

    $mail->send();
    echo "Mail envoyé avec succès à $email.";
} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur lors de l'envoi du mail : {$mail->ErrorInfo}";
    $state = "failed";
    $data = array(
        'error' => $username,
        'type' => $type,
        'id' => $id,
        'errorMessage' => $mail->ErrorInfo,
        'email' => $email
    );
    custom_log("ERROR", "Erreur lors de l'envoi du mail : {$mail->ErrorInfo}", __FILE__);
}

$result = array(
    'state' => $state,
    'data' => $data
);

echo json_encode($result);
?>