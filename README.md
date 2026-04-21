# Gestion Logistique

## Description

Application web de gestion logistique développée en PHP pour le suivi des stocks, des fournisseurs, des entrées/sorties et des rapports. Elle propose des tableaux de bord selon le rôle utilisateur : administrateur, direction, magasinier, et autres.

## Fonctionnalités principales

- Gestion des articles/pièces
- Gestion des fournisseurs
- Enregistrement des entrées de stock
- Enregistrement des sorties de stock
- Gestion des utilisateurs (role ADMIN)
- Rapports métier et tableaux de bord
- Authentification utilisateur

## Technologies

- PHP 8+ avec PDO
- MySQL / MariaDB
- Bootstrap 5
- DataTables
- Select2
- JavaScript / jQuery

## Installation

1. Installer un serveur local Apache + PHP + MySQL (par exemple XAMPP ou LAMP).
2. Copier le dossier `logistique` dans le répertoire web du serveur (`htdocs` pour XAMPP).
3. Créer une base de données MySQL pour l'application.
4. Modifier le fichier `Models/Connexion.php` pour renseigner les informations de connexion à la base de données :
   - hôte
   - nom de la base
   - utilisateur
   - mot de passe
5. Importer la structure de tables et les données nécessaires si un script SQL est fourni séparément.
6. Vérifier que le serveur Apache est en marche et ouvrir l'application dans un navigateur :
   - `http://localhost/logistique/login.php`

## Structure du projet

- `index.php` : page d'accueil après authentification, gestion des pages internes
- `login.php` : formulaire de connexion
- `logout.php` : déconnexion
- `header.php` : inclusion des dépendances et des classes métier
- `Models/` : classes PHP métiers et accès à la base de données
- `page/` : pages internes pour tableaux de bord et gestion des entités

## Rôles et accès

- `ADMIN` : accès complet, gestion des utilisateurs et rapports
- `direction` : accès aux rapports globaux
- `magasinier` : accès aux entrées, sorties et gestion d'articles

> Note : les pages et les options du menu sont affichées dynamiquement selon le rôle connecté.

## Personnalisation

- Adapter les pages et les modules métier dans le dossier `page/`
- Ajouter des validations et sécuriser davantage les formulaires si nécessaire
- Compléter les scripts SQL et la gestion des droits utilisateur

## Remarques

- Le fichier `Models/Connexion.php` contient la configuration PDO et doit être mis à jour avant utilisation.
- Le projet s'appuie sur des librairies front-end chargées via CDN.
- Vérifier que `session_start()` est bien appelé et que `$_SESSION['role']` est géré correctement.
