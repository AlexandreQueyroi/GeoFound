<?php
namespace App\Controllers;

use App\Models\Auth;
use App\Models\EmailVerification;
use App\Helpers\UserStatusManager;
use App\Helpers\EmailSender;
use App\Helpers\Logger;
use App\Models\Permission;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudoOrEmail = $_POST['pseudo'] ?? '';
            $password = $_POST['password'] ?? '';
            
            
            $user = Auth::login($pseudoOrEmail, $password);
            
            if ($user) {
                
                if (!$user['email_verified']) {
                    
                    Logger::info("Connexion compte non vérifié: {$user['pseudo']}", 'AuthController::login');
                }
                
                
                if ($user['desactivated'] == 1) {
                    $_SESSION['login_error'] = "🚫 Accès refusé : Votre compte a été banni par l'administration.";
                    $_SESSION['show_login_modal'] = true;
                    header('Location: ' . ($_SESSION['last_url'] ?? '/'));
                    exit;
                }
                
                
                if ($user['desactivated'] == 2) {
                    \App\Helpers\UserStatusManager::reactivateUserOnLogin($user['id']);
                }
                
                
                \App\Helpers\UserStatusManager::updateLastConnection($user['id']);
                
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['pseudo'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_rank'] = $user['user_rank'];
                $_SESSION['permissions'] = Permission::getUserPermissions($user['id']);
                
                
                $redirect = $_SESSION['last_url'] ?? '/';
                unset($_SESSION['last_url']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION['login_error'] = "Identifiants incorrects";
                $_SESSION['show_login_modal'] = true;
                header('Location: ' . ($_SESSION['last_url'] ?? '/'));
                exit;
            }
        }
        header('Location: /');
        exit;
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['newuser'] ?? '');
            $email = trim($_POST['newmail'] ?? '');
            $password = $_POST['newpass'] ?? '';
            $confirmPassword = $_POST['confirmpass'] ?? '';
            
            
            $errors = [];
            
            if (empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            } elseif (strlen($username) < 3) {
                $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            }
            
            if (empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            
            $captchaAnswer = trim($_POST['captcha-answer'] ?? '');
            if (empty($captchaAnswer)) {
                $errors[] = "Veuillez répondre au captcha";
            } else {
                
                $db = \App\Helpers\Database::getConnection();
                $stmt = $db->prepare("SELECT response FROM captcha WHERE enabled = 1 AND LOWER(response) = LOWER(?)");
                $stmt->execute([$captchaAnswer]);
                if (!$stmt->fetch()) {
                    $errors[] = "Réponse au captcha incorrecte";
                }
            }
            
            
            $db = \App\Helpers\Database::getConnection();
            
            $stmt = $db->prepare("SELECT id FROM users WHERE pseudo = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = "Ce nom d'utilisateur est déjà utilisé";
            }
            
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé";
            }
            
            if (!empty($errors)) {
                $_SESSION['register_errors'] = $errors;
                $_SESSION['register_data'] = ['username' => $username, 'email' => $email];
                header('Location: /auth/register');
                exit;
            }
            
            
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->prepare("
                    INSERT INTO users (pseudo, email, password, email_verified) 
                    VALUES (?, ?, ?, FALSE)
                ");
                $stmt->execute([$username, $email, $hashedPassword]);
                
                $userId = $db->lastInsertId();
                
                
                $token = EmailVerification::generateToken($userId);
                
                
                $emailSent = false;
                try {
                    $emailSent = EmailSender::sendVerificationEmail($email, $username, $token);
                } catch (Exception $e) {
                    Logger::error("Erreur envoi email: " . $e->getMessage(), 'AuthController::register');
                }
                
                if ($emailSent) {
                    $_SESSION['register_success'] = "✅ Compte créé avec succès ! Un email de validation a été envoyé à $email. Veuillez cliquer sur le lien dans l'email pour activer votre compte.";
                } else {
                    $_SESSION['register_success'] = "✅ Compte créé avec succès ! L'email de validation n'a pas pu être envoyé, mais votre compte est actif. Vous pouvez vous connecter directement.";
                    
                    
                    $stmt = $db->prepare("UPDATE users SET email_verified = TRUE WHERE id = ?");
                    $stmt->execute([$userId]);
                }
                
                Logger::info("Nouveau compte créé: $username ($email)", 'AuthController::register');
                
                header('Location: /auth/register');
                exit;
                
            } catch (\Exception $e) {
                Logger::error("Erreur lors de la création du compte: " . $e->getMessage(), 'AuthController::register');
                $_SESSION['register_errors'] = ["Une erreur est survenue lors de la création du compte. Veuillez réessayer."];
                header('Location: /auth/register');
                exit;
            }
        }
        
        
        require __DIR__ . '/../Views/auth/register.php';
    }
    
    public function verify() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['verification_error'] = "Token de validation manquant.";
            header('Location: /');
            exit;
        }
        
        $userId = EmailVerification::validateToken($token);
        
        if ($userId) {
            
            $db = \App\Helpers\Database::getConnection();
            $stmt = $db->prepare("SELECT pseudo, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if ($user) {
                
                EmailSender::sendWelcomeEmail($user['email'], $user['pseudo']);
                
                $_SESSION['verification_success'] = "🎉 Votre compte a été validé avec succès ! Vous pouvez maintenant vous connecter.";
                Logger::info("Compte validé: {$user['pseudo']} ({$user['email']})", 'AuthController::verify');
            }
        } else {
            $_SESSION['verification_error'] = "Token de validation invalide ou expiré.";
        }
        
        header('Location: /');
        exit;
    }
    
    public function resendVerification() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['resend_error'] = "Email invalide.";
                header('Location: /auth/register');
                exit;
            }
            
            $db = \App\Helpers\Database::getConnection();
            $stmt = $db->prepare("SELECT id, pseudo, email_verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $_SESSION['resend_error'] = "Aucun compte trouvé avec cet email.";
                header('Location: /auth/register');
                exit;
            }
            
            if ($user['email_verified']) {
                $_SESSION['resend_error'] = "Ce compte est déjà validé.";
                header('Location: /auth/register');
                exit;
            }
            
            
            $token = EmailVerification::generateToken($user['id']);
            
            
            if (EmailSender::sendVerificationEmail($email, $user['pseudo'], $token)) {
                $_SESSION['resend_success'] = "Un nouvel email de validation a été envoyé à $email.";
            } else {
                $_SESSION['resend_error'] = "Impossible d'envoyer l'email de validation.";
            }
        }
        
        header('Location: /auth/register');
        exit;
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
} 