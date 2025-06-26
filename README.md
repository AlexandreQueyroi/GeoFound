# GeoFound - Architecture MVC

## Structure du projet

```
geofound/
├── app/
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Models/         # Modèles de données
│   ├── Views/          # Vues PHP/HTML
│   └── Helpers/        # Fonctions utilitaires
├── config/
│   ├── database.php    # Configuration BDD
│   └── routes.php      # Définition des routes
├── public/
│   ├── index.php       # Point d'entrée unique
│   ├── .htaccess       # URL rewriting
│   └── assets/         # Ressources statiques
│       ├── css/
│       ├── js/
│       ├── img/
│       └── avatars/
├── storage/
│   ├── logs/           # Fichiers de logs
│   ├── uploads/        # Fichiers uploadés
│   ├── database.sql    # Structure BDD
│   └── delete_database.sql
└── vendor/             # Dépendances Composer
```

## Routes disponibles

- `/` - Page d'accueil
- `/user/edit/{id}` - Édition profil utilisateur
- `/user/inbox` - Messagerie utilisateur
- `/admin` - Panel d'administration
- `/admin/rank` - Gestion des rangs
- `/admin/user` - Gestion des utilisateurs
- `/post` - Liste des posts
- `/friend/{id}` - Liste des amis
- `/message/inbox/{id}` - Boîte de réception
- `/message/view/{userId}/{friendId}` - Conversation
- `/login` - Connexion
- `/register` - Inscription
- `/logout` - Déconnexion
- `/api/user` - API utilisateur
- `/api/captcha` - API captcha
- `/reward` - Récompenses
- `/reward/unlock/{id}` - Débloquer récompense
- `/avatar` - Gestion avatars

## Installation

1. Configurer la base de données dans `config/database.php`
2. Importer `storage/database.sql`
3. Configurer le serveur web pour pointer vers `public/`
4. Installer les dépendances : `composer install`

## Utilisation

Le point d'entrée est `public/index.php` qui charge le routeur et dirige vers le bon contrôleur selon l'URL. 