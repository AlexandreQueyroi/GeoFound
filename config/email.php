<?php

return [
    'smtp' => [
        'host' => 'smtp.gmail.com', 
        'port' => 587, 
        'encryption' => 'tls', 
        'username' => 'alexandregeofound@gmail.com', 
        'password' => 'vtaakhznwkorpknn', 
        'from_email' => 'alexandregeofound@gmail.com', 
        'from_name' => 'GeoFound', 
    ],
    
    
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