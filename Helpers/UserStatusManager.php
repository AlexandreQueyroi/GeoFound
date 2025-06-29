<?php

namespace App\Helpers;

class UserStatusManager {
    
    /**
     * Vérifie et met à jour automatiquement les statuts des utilisateurs
     * - Désactive les utilisateurs inactifs depuis plus d'1 jour
     * - Envoie des emails de notification
     */
    public static function checkAndUpdateUserStatuses() {
        try {
            $db = Database::getConnection();
            
            // Désactiver les utilisateurs inactifs depuis plus d'1 jour
            $stmt = $db->prepare("
                UPDATE users 
                SET desactivated = 2 
                WHERE desactivated = 0 
                AND connected IS NOT NULL 
                AND connected < DATE_SUB(NOW(), INTERVAL 1 DAY)
            ");
            $stmt->execute();
            $inactiveCount = $stmt->rowCount();
            
            // Envoyer des emails aux utilisateurs désactivés
            if ($inactiveCount > 0) {
                $stmt = $db->prepare("
                    SELECT id, pseudo, email 
                    FROM users 
                    WHERE desactivated = 2 
                    AND email IS NOT NULL 
                    AND email != ''
                ");
                $stmt->execute();
                $inactiveUsers = $stmt->fetchAll();
                
                foreach ($inactiveUsers as $user) {
                    self::sendInactiveNotificationEmail($user);
                }
            }
            
            // Log des actions
            if ($inactiveCount > 0) {
                $logMessage = date('[d/m/Y H:i:s] ') . "Gestion automatique: $inactiveCount utilisateur(s) désactivé(s) pour inactivité\n";
                file_put_contents(__DIR__ . '/../storage/logs/user_status.log', $logMessage, FILE_APPEND);
            }
            
            return $inactiveCount;
            
        } catch (\Exception $e) {
            $logMessage = date('[d/m/Y H:i:s] ') . "Erreur gestion automatique statuts: " . $e->getMessage() . "\n";
            file_put_contents(__DIR__ . '/../storage/logs/user_status.log', $logMessage, FILE_APPEND);
            return 0;
        }
    }
    
    /**
     * Envoie un email de notification pour compte inactif
     */
    private static function sendInactiveNotificationEmail($user) {
        $to = $user['email'];
        $subject = "Votre compte GeoFound a été désactivé";
        
        $message = "
        <html>
        <head>
            <title>Compte désactivé</title>
        </head>
        <body>
            <h2>Bonjour {$user['pseudo']},</h2>
            <p>Votre compte GeoFound a été automatiquement désactivé car vous n'avez pas été connecté depuis plus d'un jour.</p>
            <p>Pour réactiver votre compte, connectez-vous simplement à votre compte.</p>
            <p>Si vous avez des questions, n'hésitez pas à contacter l'équipe de support.</p>
            <br>
            <p>Cordialement,<br>L'équipe GeoFound</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@geofound.com\r\n";
        
        mail($to, $subject, $message, $headers);
    }
    
    /**
     * Vérifie si un utilisateur peut se connecter
     * Retourne true si l'utilisateur peut se connecter, false sinon
     */
    public static function canUserLogin($userId) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("SELECT desactivated FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return false;
            }
            
            // Les utilisateurs bannis (desactivated = 1) ne peuvent pas se connecter
            return $user['desactivated'] != 1;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Réactive un utilisateur inactif lors de sa connexion
     */
    public static function reactivateUserOnLogin($userId) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("
                UPDATE users 
                SET desactivated = 0, connected = NOW() 
                WHERE id = ? AND desactivated = 2
            ");
            $stmt->execute([$userId]);
            
            return $stmt->rowCount() > 0;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public static function updateLastConnection($userId) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("UPDATE users SET connected = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
            
            return true;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtient le texte du statut
     */
    public static function getStatusText($statusCode) {
        switch ($statusCode) {
            case 0:
                return 'Actif';
            case 1:
                return 'Banni';
            case 2:
                return 'Inactif';
            default:
                return 'Inconnu';
        }
    }
    
    /**
     * Obtient la classe CSS pour le statut
     */
    public static function getStatusClass($statusCode) {
        switch ($statusCode) {
            case 0:
                return 'bg-green-100 text-green-800';
            case 1:
                return 'bg-red-100 text-red-800';
            case 2:
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
} 