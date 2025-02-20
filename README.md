GeoFound

Projet Annuel de 1ère année à l'ESGI

## Table des Matières

* [1. Présentation du projet](#1-pr%C3%A9sentation-du-projet)
  * [1.1 Nom du projet](#11-nom-du-projet)
  * [1.2 Description Générale du Projet](#12-Description-G%C3%A9n%C3%A9rale-du-Projet)
* [2. Fonctionnalités](#2-Fonctionnalit%C3%A9s)
  * [2.1 Espace Utilisateur](#21-Espace-Utilisateur)
    * [2.1.1 Page D'accueil](#211-Page-D-accueil)
    * [2.1.2 Page de Connexion](#212-Page-de-Connexion)
    * [2.1.3 Page de Profil](#213-Page-de-Profil)
    * [2.1.4 Page de Messagerie](#214-Page-de-Messagerie)
    * [2.1.5 Recherche Utilisateur](#215-Recherche-Utilisateur)
    * [2.1.6 Page de Récompenses](#216-Page-de-R%C3%A9compenses)
    * [2.1.7 Formulaire de Contact](#217-Formulaire-de-Contact)
    * [2.1.8 Politique de Confidentialité](#218-Politique-de-Confidentialit%C3%A9)
    * [2.1.9 Page des CGU](#219-Page-des-CGU)
  * [2.2 Espace Modération](#22-Espace-Mod%C3%A9ration)
    * [2.2.1 Page Signalement](#221-Page-Signalement)
    * [2.2.2 Page Messagerie Globale](#222-Page-Messagerie-Globale)
    * [2.2.3 Page Modération des Posts](#223-Page-Mod%C3%A9ration-des-Posts)
    * [2.2.4 Page Utilisateurs (Modération)](#224-Page-Utilisateurs-Mod%C3%A9ration)
    * [2.2.5 Page Sanctions Utilisateurs](#225-Page-Sanctions-Utilisateurs)
  * [2.3 Espace Administration](#23-Espace-Administration)
    * [2.3.1 Page Maintenance Site](#231-Page-Maintenance-Site)
    * [2.3.2 Page Utilisateurs (Administration)](#232-Page-Utilisateurs-Administration)
    * [2.3.3 Page Gestion Utilisateurs](#233-Page-Gestion-Utilisateurs)
    * [2.3.4 Page Gestion des Captcha](#234-Page-Gestion-des-Captcha)
    * [2.3.5 Page logs du site](#235-Page-logs-du-site)
    * [2.3.6 Page Statistiques](#236-Page-Statistiques)
    * [2.3.7 Page Tickets Développeurs](#237-Page-Tickets-D%C3%A9veloppeurs)
* [3. Contraintes Techniques](#3-Contraintes-Techniques)
  * [3.1 Technologies Utilisés](#31-Technologies-Utilis%C3%A9s)
  * [3.2 Sécurité](#32-S%C3%A9curit%C3%A9)
* [4. Conclusion](#4-Conclusion)
* [5. Prérequis et Installation](#5Pr%C3%A9requis-et-Installation)


---

## 1. Présentation du projet

### 1.1 Nom du projet

Le nom du projet est **GeoFound.**

### 1.2 Description Générale du Projet

GeoFound est une plateforme sociale conçue pour les amateurs de voyages et d'exploration. Il offre aux utilisateurs la possibilité de partager leurs expériences en utilisant des images, des vidéos et des textes géolocalisés. La plateforme propose un système d'interaction dynamique qui comprend le suivi d'autres utilisateurs, une messagerie privée, ainsi que des options de mention j'aime, abonnement et rapport.

Un moteur de recherche intelligent, utilisant l'API Google Maps, offre des contenus personnalisés en fonction des destinations que l'utilisateur souhaite explorer. Une map mondiale interactive donne la possibilité de consulter l'intégralité des publications disponibles sur la plateforme.

GeoFound dispose également d'un blog où les utilisateurs ont la possibilité de commenter les photos et vidéos publiées, grâce à un système de vote destiné à promouvoir les contenus de qualité. Un système de fidélité axé sur l'activité des utilisateurs propose des goodies et des remises pour leurs prochaines excursions.

Finalement, une interface d'administration et de modération permet de gérer les contenus et les comptes, assurant ainsi une expérience engageante et sécurisée pour la communauté.

## 2. Fonctionnalités

### 2.1 Espace Utilisateur

#### 2.1.1 Page D'accueil

* Map Monde via l’API de Google Map montrant toute les photos enregistrés par les utilisateurs, en fonction de leur  localisation.
* Moteur de recherche utilisant l’API Google Map pour trouver des images en fonction d’une destination, montrant les images proches de cette destination (Ajouter un curseur permettant de choisir le rayon max ?)
* Navbar avec les pages [2.1.2 Page de Connexion](#212-Page-de-Connexion), \[2.1.5 Recherche Utilisateur\](https://2.1.5 Recherche Utilisateur), [2.1.3 Page de Profil](#213-Page-de-Profil), [2.1.6 Page de Récompenses](#216-Page-de-R%C3%A9compenses), \[2.1.4 Page de Messagerie\](https://2.1.4 Page de Messagerie). Ajout d’un onglet “Manager” pour les personnes ayant un accès de modération et/ou d’administration du site.
* Footer avec la page menant vers [2.1.8 Politique de Confidentialité](#218-Politique-de-Confidentialit%C3%A9), [2.1.9 Page des CGU](#219-Page-des-CGU), [2.1.7 Formulaire de Contact](#217-Formulaire-de-Contact)

#### 2.1.2 Page de Connexion

* Modal permettant la connexion / l’inscription d’un utilisateur (switch entre les 2 via un bouton “Inscription” / “Connexion”
* Vérification de l’adresse mail de l’utilisateur (envoi d’un code à son adresse mail, utilisable pendant X temps
* (Utilisation de l’A2F pour les personnes ayant des permissions sur le site (Mod, Admin) ?)
* Possibilité d’enregistrement automatique de l’utilisateur / du mot de passe via google, bitwarden, …

#### 2.1.3 Page de Profil

* Accès à différentes parties :
  * Modifier son Compte
    * Modification de l’avatar
    * Modification du nom d’utilisateur
    * Modification du mail
    * Modification du mot de passe

      Chacune des modification si-dessus (hormis l’avatar) demandera la saisie du mot de passe pour être effective)
  * Désactiver son Compte (Plus de mail de la newsletter, plus de mail de confirmation après une période d’inactivité) mais toute les données sont encore sauvegardé)
  * Supprimer son Compte (Désactivation de l’utilisateur puis stockage des données dans une base de donnée annexe)
  * Voir ses publication
  * Voir les publications de ses amis
  * Télécharger toute ses données (Photos publiés, messages postés, …) dans un pdf
  * Voir son nombre de points actuel (+ son total de points récolté)

#### 2.1.4 Page de Messagerie

* Liste de ses amis
* (Possibilité d’activer les notifications sur l’ordinateur de la personne ?)
* Affichage de la conversation avec un utilisateur quand on clique dessus
  * Like de Message
  * Supprimer ses messages (garder en bdd pour log)
  * Voir si un de ses amis est connecté (+ recevoir notif à sa connexion ?)

#### 2.1.5 Recherche Utilisateur

* Pouvoir chercher les données d’un utilisateur (Photos, Vidéos et Textes posté, ses commentaires, ses likes s’ils sont en publique)
* Pouvoir l’ajouter en amis
* Pouvoir le signaler (mettre des suggestions de signalement, “Propos Incorrects”, “Compte Piraté”, ….)

#### 2.1.6 Page de Récompenses

* Afficher une liste de produit, automatiquement en fonction de la bdd. (Gestion des stocks dans back office)
* Pouvoir “acheter” avec un nombre de points X une récompense et/ou un goodies. → Envoie d’un mail de confirmation de la réception de la commande
* Voir son nombre de points sur cette page également

#### 2.1.7 Formulaire de Contact

Possibilité de contact le support du site pour plusieurs motifs défini (Découverte d’un bug, Demande d’affiliation, …, catégorie “Autre” qui affiche un nouveau champ de texte pour indiquer sa demande) → Envoie de ce formulaire sur le back office + confirmation par mail à l’utilisateur. Réponse via back office avec envoie de mail à l’utilisateur.

#### 2.1.8 Politique de Confidentialité

Affichage des politiques de confidentialités du site

#### 2.1.9 Page des CGU

Affichage des CGU du site

### 2.2 Espace Modération

#### 2.2.1 Page Signalement

* Barre de recherche pour rechercher un signalement
* Filtre pour afficher que certains signalement “Non traité”, “En cours”, “Traité”)
* Affichage sous forme d’une liste des signalements → `#ID_SIGNALEMENT PARTIE_SITE_CONCERNÉ UTILISATEUR (STATUT)`
  * Informations complémentaire quand on clique dessus :
    * Signalement traité par : X
    * Date du signalement :
    * Description du signalement :
    * Commentaires de la personne s’en occupant

#### 2.2.2 Page Messagerie Globale

Accès à une page permettant de prendre l’apparence de n’importe quel utilisateur

* Vision de la page de profil de la personne
* Possibilité de voir les messages privés de la personne
* Switch Permettant d’afficher soit les messages privés soit les posts/ commentaires de l’utilisateur

#### 2.2.3 Page Modération des Posts

* Affichage d’un engrenage sur tout les posts/ commentaires d’utilisateurs ayant une permission inférieur à la sienne permettant de :
  * Supprimer le message
  * Supprimer le message et sanctionner le joueur (Supprime le message puis redirige le modérateur sur la page des sanctions, avec l’utilisateur déjà rentré en paramètre.

#### 2.2.4 Page Utilisateurs Modération

* Voir la liste de tout les utilisateurs enregistré
* Voir leur avatar, possibilité de le réinitialiser
* Voir leur poste en cliquant sur l’utilisateur
* Voir la liste de leurs commentaires
* Avoir un raccourcis vers le panel de sanctions

#### 2.2.5 Page Sanctions Utilisateurs

* Possibilité d’avertir l’utilisateur → Lui informer d’une action qu’il a commis une action contraire aux règles du site, il n’a pas de “vrai” sanction avec mais il est simplement informé
* Rendre muet l’utilisateur → L’empêcher de pouvoir poster / réagir aux posts des utilisateurs pendant une certaine durée

  Bannir l’utilisateur → Ne peut plus réagir aux posts, ne peut plus upvote, ne peut plus réclamer de récompenses, à ses posts caché pour les utilisateurs lambdas. Stocker son email et son utilisateur pour qu’il ne puisse pas supprimer et recréér son compte pour contourner le bannissement.
* Voir l’historique des sanctions d’un utilisateur
* Supprimer une sanction (Administration)

### 2.3 Espace Administration

#### 2.3.1 Page Maintenance Site

Voir la liste de toute les pages accessibles par les utilisateurs lambdas, pouvant les mettre en maintenance (redirection vers /maintenance?redirectfrom=PAGEID) pour l’afficher à l’utilisateur

#### 2.3.2 Page Utilisateurs Administration

* Même accès que les modérateurs +
* Voir leur adresse IP actuelle
* Possibilité de supprimer des sanctions / toute les données d’un utilisateur

#### 2.3.3 Page Gestion Utilisateurs

* Possibilité d’envoyer un mail pour réinitialiser leur mot de passe
* Possibilité de désactiver/ supprimer leur compte
* Possibilité de changer les permissions de l’utilisateur (Contributeur, Modérateur, Administrateur, …)

#### 2.3.4 Page Gestion des Captcha

* Possibilité de voir le taux de réussite des captcha (global / individuel)
* Possibilité d’ajouter/supprimer un captcha
* Possibilité de consulter la liste des captcha

#### 2.3.5 Page logs du site

* Logs de création de compte
* Logs de modification de compte
* Logs de connexion/ déconnexion
* Logs d’ajout/ modification de post
* Logs de désactivation / suppression de comptes

#### 2.3.6 Page Statistiques

* Statistique de visites journalières
* Statistique de post moyen / utilisateur
* …

#### 2.3.7 Page Tickets Développeurs

Utilisation de GLPI pour gérer les tickets développeurs

## 3. Contraintes Techniques

### 3.1 Technologies Utilisés

* Front → HTML, CSS, Tailwind CSS
* Back → PHP, JS
* API → Google Map API, PHPMailer
* Outils de coordinations → Github, Figma, Jira, Discord

### 3.2 Sécurité

* Protection contre les failles SQL
* Protection contre les requêtes non autorisés

## 4. Conclusion

En conclusion, le projet GeoFound représente une plateforme sociale innovante dédiée aux passionnés de voyages et d'exploration. Grâce à ses nombreuses fonctionnalités, telles que la géolocalisation des publications, la messagerie privée, et un système de récompenses, GeoFound offre une expérience utilisateur riche et interactive. L'intégration de l'API Google Maps permet une recherche intuitive et personnalisée des contenus, tandis que les espaces de modération et d'administration assurent la sécurité et la qualité des interactions sur la plateforme. Les contraintes techniques et les mesures de sécurité mises en place garantissent un environnement fiable et performant. GeoFound se positionne ainsi comme un outil incontournable pour les amateurs de découvertes et d'aventures à travers le monde.


---


