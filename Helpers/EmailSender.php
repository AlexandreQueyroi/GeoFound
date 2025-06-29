<?php
namespace App\Helpers;

if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    
    /**
     * Configure et retourne une instance PHPMailer
     */
    private static function getMailer() {
        $mail = new PHPMailer(true);
        
        try {
            // Charger la configuration
            $config = include __DIR__ . '/../config/email.php';
            $smtp = $config['smtp'];
            
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = $smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $smtp['port'];
            $mail->CharSet = 'UTF-8';
            
            // Configuration de l'expéditeur
            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
            $mail->addReplyTo($smtp['from_email'], $smtp['from_name']);
            
            return $mail;
        } catch (Exception $e) {
            throw new \Exception("Erreur de configuration PHPMailer: " . $e->getMessage());
        }
    }
    
    /**
     * Envoie un email de validation de compte
     */
    public static function sendVerificationEmail($email, $username, $token) {
        try {
            $mail = self::getMailer();
            
            // Destinataire
            $mail->addAddress($email, $username);
            
            // Sujet
            $mail->Subject = "GeoFound - Validation de votre compte";
            
            // URL de validation
            $host = $_SERVER['HTTP_HOST'] ?? 'geofound.fr';
            $verificationUrl = "https://" . $host . "/auth/verify?token=" . $token;
            
            // Corps du message HTML
            $mail->isHTML(true);
            $mail->Body = "
            <html>
            <head>
                <title>Validation de votre compte GeoFound</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                    .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎉 Bienvenue sur GeoFound !</h1>
                    </div>
                    <div class='content'>
                        <h2>Bonjour $username,</h2>
                        <p>Merci de vous être inscrit sur GeoFound ! Pour activer votre compte et commencer à explorer le monde, veuillez cliquer sur le bouton ci-dessous :</p>
                        
                        <div style='text-align: center;'>
                            <a href='$verificationUrl' class='button'>✅ Valider mon compte</a>
                        </div>
                        
                        <p><strong>Ce lien expire dans 24 heures.</strong></p>
                        
                        <p>Si le bouton ne fonctionne pas, vous pouvez copier-coller ce lien dans votre navigateur :</p>
                        <p style='word-break: break-all; color: #667eea;'>$verificationUrl</p>
                        
                        <p>Si vous n'avez pas créé de compte sur GeoFound, vous pouvez ignorer cet email.</p>
                    </div>
                    <div class='footer'>
                        <p>© 2024 GeoFound - Découvrez le monde qui vous entoure</p>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Version texte
            $mail->AltBody = "
            Bienvenue sur GeoFound !
            
            Bonjour $username,
            
            Merci de vous être inscrit sur GeoFound ! Pour activer votre compte et commencer à explorer le monde, veuillez cliquer sur le lien ci-dessous :
            
            $verificationUrl
            
            Ce lien expire dans 24 heures.
            
            Si vous n'avez pas créé de compte sur GeoFound, vous pouvez ignorer cet email.
            
            © 2024 GeoFound - Découvrez le monde qui vous entoure
            ";
            
            // Envoi
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            Logger::error("Erreur envoi email validation: " . $e->getMessage(), 'EmailSender::sendVerificationEmail');
            return false;
        }
    }
    
    /**
     * Envoie un email de bienvenue après validation
     */
    public static function sendWelcomeEmail($email, $username) {
        try {
            $mail = self::getMailer();
            
            // Destinataire
            $mail->addAddress($email, $username);
            
            // Sujet
            $mail->Subject = "GeoFound - Votre compte est maintenant actif !";
            
            // Corps du message HTML
            $mail->isHTML(true);
            $mail->Body = "
            <html>
            <head>
                <title>Bienvenue sur GeoFound</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                    .button { display: inline-block; background: #22c55e; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎉 Votre compte est maintenant actif !</h1>
                    </div>
                    <div class='content'>
                        <h2>Félicitations $username !</h2>
                        <p>Votre compte GeoFound a été validé avec succès. Vous pouvez maintenant :</p>
                        
                        <ul>
                            <li>📱 Créer des posts pour partager vos découvertes</li>
                            <li>🗺️ Explorer les posts des autres utilisateurs</li>
                            <li>💬 Commenter et interagir avec la communauté</li>
                            <li>👥 Ajouter des amis et échanger en privé</li>
                            <li>🏆 Débloquer des récompenses</li>
                        </ul>
                        
                        <div style='text-align: center;'>
                            <a href='http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "' class='button'>🚀 Commencer l'exploration</a>
                        </div>
                        
                        <p>Bonne exploration sur GeoFound !</p>
                    </div>
                    <div class='footer'>
                        <p>© 2024 GeoFound - Découvrez le monde qui vous entoure</p>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Version texte
            $mail->AltBody = "
            Votre compte est maintenant actif !
            
            Félicitations $username !
            
            Votre compte GeoFound a été validé avec succès. Vous pouvez maintenant :
            
            - Créer des posts pour partager vos découvertes
            - Explorer les posts des autres utilisateurs
            - Commenter et interagir avec la communauté
            - Ajouter des amis et échanger en privé
            - Débloquer des récompenses
            
            Commencez l'exploration : http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "
            
            Bonne exploration sur GeoFound !
            
            © 2024 GeoFound - Découvrez le monde qui vous entoure
            ";
            
            // Envoi
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            Logger::error("Erreur envoi email bienvenue: " . $e->getMessage(), 'EmailSender::sendWelcomeEmail');
            return false;
        }
    }
} 