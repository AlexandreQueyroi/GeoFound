<?php
include_once(__DIR__ . '/api/bdd.php');
try {
    $check = $conn->query("SHOW COLUMNS FROM message LIKE 'mail_sent'");
    if ($check->rowCount() == 0) {
        $conn->exec("ALTER TABLE message ADD COLUMN mail_sent TINYINT(1) DEFAULT 0");
        echo "Colonne 'mail_sent' ajoutée à la table message.<br>";
    } else {
        echo "La colonne 'mail_sent' existe déjà.<br>";
    }
    $conn->exec("UPDATE message SET mail_sent = 0 WHERE mail_sent IS NULL");
    echo "Tous les messages ont mail_sent à 0 si besoin.<br>";
    echo "Migration terminée.";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage();
}
?> 