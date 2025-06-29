<?php
/**
 * Configuration des emails
 */

return [
    'smtp' => [
        'host' => 'smtp.gmail.com', // Serveur SMTP
        'port' => 587, // Port SMTP
        'encryption' => 'tls', // Type de chiffrement
        'username' => 'alexandregeofound@gmail.com', // Email SMTP
        'password' => 'vtaakhznwkorpknn', // Mot de passe SMTP
        'from_email' => 'alexandregeofound@gmail.com', // Email d'expédition
        'from_name' => 'GeoFound', // Nom d'expédition
    ],
    
    // Configuration pour différents fournisseurs
    'providers' => [
        'gmail' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
        ],
        'outlook' => [
            'host' => 'smtp-mail.outlook.com',
            'port' => 587,
            'encryption' => 'tls',
        ],
        'yahoo' => [
            'host' => 'smtp.mail.yahoo.com',
            'port' => 587,
            'encryption' => 'tls',
        ],
        'sendgrid' => [
            'host' => 'smtp.sendgrid.net',
            'port' => 587,
            'encryption' => 'tls',
        ],
    ]
]; 