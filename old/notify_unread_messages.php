<?php
include_once(__DIR__ . '/api/bdd.php');

try {
    $messages = $conn->query('
        SELECT m.id, m.content, m.posted_at, m.mail_sent, u_from.pseudo AS sender_pseudo, u_to.pseudo AS receiver_pseudo, u_to.email AS receiver_email, um.sender_id, um.receiver_id
        FROM message m
        JOIN user_message um ON m.id = um.message_id
        JOIN users u_from ON um.sender_id = u_from.id
        JOIN users u_to ON um.receiver_id = u_to.id
        WHERE m.state = "sent" AND m.mail_sent = 0 AND m.posted_at < (NOW() - INTERVAL 5 MINUTE)
    ')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $msg) {
        $data = [
            'type' => 'notifyUnreadMessage',
            'sender_pseudo' => $msg['sender_pseudo'],
            'receiver_pseudo' => $msg['receiver_pseudo'],
            'receiver_email' => $msg['receiver_email'],
            'content' => $msg['content'],
            'link' => 'https:
        ];

        ob_start();
        $_POST = $data;
        include(__DIR__ . '/api/PHPMailer.php');
        $result = ob_get_clean();
        
        if ($result !== false) {
            $response = json_decode($result, true);
            if ($response === null) {
                error_log("Réponse JSON invalide pour le message {$msg['id']}: " . $result);
                echo 'Erreur de format de réponse pour ' . $msg['receiver_email'] . ': ' . $result . '<br>';
                continue;
            }
            
            if ($response['state'] === 'success') {
                try {
                    $stmt = $conn->prepare('UPDATE message SET mail_sent = 1 WHERE id = ?');
                    $success = $stmt->execute([$msg['id']]);
                    
                    if ($success) {
                        echo 'Mail envoyé avec succès à ' . $msg['receiver_email'] . ' et statut mis à jour.<br>';
                    } else {
                        error_log("Échec de la mise à jour du statut pour le message {$msg['id']}");
                        echo 'Mail envoyé mais erreur lors de la mise à jour du statut pour ' . $msg['receiver_email'] . '<br>';
                    }
                } catch (PDOException $e) {
                    error_log("Erreur PDO lors de la mise à jour du statut pour le message {$msg['id']}: " . $e->getMessage());
                    echo 'Mail envoyé mais erreur lors de la mise à jour du statut pour ' . $msg['receiver_email'] . ': ' . $e->getMessage() . '<br>';
                }
            } else {
                $error = $response['error'] ?? 'Erreur inconnue';
                $details = isset($response['details']) ? json_encode($response['details']) : '';
                error_log("Erreur d'envoi de mail pour le message {$msg['id']}: " . $error . " - Détails: " . $details);
                echo 'Erreur lors de l\'envoi du mail à ' . $msg['receiver_email'] . ': ' . $error . ' - Détails: ' . $details . '<br>';
            }
        } else {
            $error = error_get_last();
            $errorMsg = $error ? $error['message'] : 'Erreur inconnue';
            error_log("Erreur de connexion à l'API PHPMailer pour le message {$msg['id']}: " . $errorMsg);
            echo 'Erreur de connexion à l\'API PHPMailer pour ' . $msg['receiver_email'] . ': ' . $errorMsg . '<br>';
        }
    }
} catch (Exception $e) {
    error_log("Erreur dans notify_unread_messages.php: " . $e->getMessage());
    echo 'Une erreur est survenue: ' . $e->getMessage();
}
?> 