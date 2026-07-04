# GSF — Gestion de Stock & Facturation

Application web de gestion de stock et suivi de la facturation, développée par **Event'24** en 2017.

## Description

GSF est un système de gestion commerciale permettant de :

- Gérer un catalogue d'articles avec catégorisation et suivi de stock (entrées, sorties, mises au rebut, retours)
- Réaliser des ventes au comptant, à crédit et à avoir
- Générer et imprimer des factures, bons de livraison et tickets de caisse
- Suivre les règlements et échéances des factures à crédit
- Gérer la petite caisse (entrées et dépenses)
- Consulter des statistiques via un tableau de bord (graphiques Highcharts)
- Administrer les utilisateurs, les droits d'accès et les paramètres de la société

## Stack technique actuelle

| Composant | Version |
|-----------|---------|
| PHP | 5.6.3 |
| MySQL | 5.6.21 |
| Bootstrap | 3.3.2 |
| jQuery | 2.1.3 |
| AdminLTE | 2.x |
| DataTables | 1.x |
| Highcharts | — |
| Font Awesome | 4.3.0 |
| FPDF / html2pdf | — |

## Prérequis

- PHP 5.6+
- MySQL 5.6+
- Serveur web Apache (ou WAMP/XAMPP/MAMP)
- phpMyAdmin (optionnel, pour importer la base)

## Installation

1. Cloner ou copier le projet dans le répertoire web (`htdocs`, `www`, etc.)
2. Créer la base de données MySQL :
   ```sql
   CREATE DATABASE bd_gestockevent24;
   ```
3. Importer le fichier `bd_gestockevent24.sql` dans la base
4. Configurer la connexion dans `php/connexion.php` (host, user, pass, db_name)
5. Accéder à l'application via le navigateur

## Structure du projet

```
gestock_event24/
├── index.php                  # Page de connexion
├── accueil.php                # Tableau de bord (dashboard)
├── patterning.php             # Template principal (layout)
├── deconnexion.php            # Déconnexion utilisateur
│
├── php/
│   ├── connexion.php          # Classe de connexion PDO
│   ├── fonction.php           # Fonctions utilitaires (requêtes, calculs factures)
│   ├── ChiffresEnLettres.php  # Conversion montant en lettres
│   ├── personnage.php         # (divers)
│   ├── fpdf/                  # Bibliothèque de génération PDF
│   ├── html2pdf/              # Conversion HTML vers PDF
│   └── Highcharts/            # Bibliothèque de graphiques
│
├── ajax/
│   ├── listarticle.ajax.php   # Liste articles (autocomplete)
│   ├── prixarticle.ajax.php   # Prix d'un article
│   ├── restestockarticle.ajax.php  # Reste en stock
│   └── selectarticle.ajax.php # Sélection article
│
├── vente*.php                 # Modules de vente (comptant, crédit, avoir)
├── caisse*.php                # Modules caisse (entrées, dépenses, états)
├── maj*.php                   # Modules de paramétrage (CRUD)
├── popup*.php                 # Fenêtres popup (impressions, détails)
├── liste*.php                 # Listes et états
├── etat*.php                  # États/rapports
│
├── bootstrap/                 # Bootstrap 3.3.2 (CSS + JS + Fonts)
├── datatables/                # Plugin DataTables
├── dist/                      # AdminLTE 2 (thème admin)
├── plugins/                   # Plugins jQuery (datepicker, sparkline, etc.)
├── build/                     # Fichiers de build AdminLTE
│
└── bd_gestockevent24.sql      # Dump de la base de données
```

## Base de données

La base `bd_gestockevent24` contient 21 tables :

| Table | Rôle |
|-------|------|
| `user` | Utilisateurs du système |
| `compte` | Types de comptes (ADMIN, CAISSE, STOCK) |
| `access` | Droits d'accès par compte/menu |
| `menu` | Menus principaux |
| `menuitem` | Sous-menus et liens |
| `entete` | Informations société (nom, adresse, TVA, logo) |
| `article` | Catalogue des articles |
| `typearticle` | Catégories d'articles |
| `client` | Clients |
| `clientbck` | Sauvegarde clients (ancienne structure) |
| `fournisseur` | Fournisseurs |
| `magasin` | Magasins/dépôts |
| `entreestock` | Mouvements d'entrée en stock |
| `drop_entreestock` | Entrées supprimées |
| `facture` | Factures (comptant, crédit, avoir) |
| `annuler_facture` | Factures annulées |
| `reglement` | Règlements et opérations de caisse |
| `modalite` | Modalités de paiement (mensuel, trimestriel, etc.) |
| `typefacture` | Types de factures |
| `rebus` | Articles mis au rebut |
| `retour` | Retours articles |

## Comptes par défaut

- **Login** : `lpatrick`
- **Type** : ADMIN (accès complet)

## Auteur

Event'24 — [event24apps.com](http://event24apps.com)

## Licence

Tous droits réservés © 2017 Event'24
