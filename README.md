# 🍽️ Restaurant Universitaire — Système de Restauration Numérique

Application web de gestion de restauration universitaire permettant aux étudiants d'acheter des tickets de repas en ligne et aux caissiers de les valider à l'entrée du restaurant.

---

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Architecture du projet](#-architecture-du-projet)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration de la base de données](#-configuration-de-la-base-de-données)
- [Rôles utilisateurs](#-rôles-utilisateurs)
- [Structure de la base de données](#-structure-de-la-base-de-données)

---

## ✨ Fonctionnalités

- **Étudiants** : inscription, connexion, achat de tickets de repas via solde D17, génération de QR code
- **Caissiers** : scan et validation des tickets QR à l'entrée du restaurant
- **Administrateurs** : gestion des menus, des repas, des utilisateurs et consultation des statistiques
- Menu journalier avec catégories (entrée, plat principal, dessert, boisson)
- Repas tunisiens préconfigurés (Couscous, Brik, Tajine, Lablebi, etc.)
- Prix fixe subventionné à **0.200 DT** par repas

---

## 🛠️ Technologies utilisées

| Couche | Technologie |
|--------|-------------|
| Backend | PHP (architecture MVC) |
| Base de données | MySQL / MariaDB |
| Frontend | HTML, CSS, JavaScript |
| URL Routing | `.htaccess` (mod_rewrite) |
| Sécurité mots de passe | `bcrypt` (via `password_hash`) |

---

## 📁 Architecture du projet

```
restaurant_universitaire/
│
├── config/               # Configuration base de données & constantes
├── controllers/          # Contrôleurs (logique métier)
├── models/               # Modèles (accès base de données)
├── views/                # Vues (templates HTML/PHP)
├── public/               # Assets statiques (CSS, JS, images)
├── index.php             # Point d'entrée de l'application
├── .htaccess             # Réécriture d'URL
└── restauration_schema.sql  # Schéma et données initiales de la BDD
```

---

## ✅ Prérequis

- PHP **8.0+**
- MySQL **5.7+** ou MariaDB **10.4+**
- Serveur web Apache avec `mod_rewrite` activé (ex : XAMPP, WAMP, Laragon)
- Extension PHP : `pdo_mysql`, `mbstring`

---

## 🚀 Installation

1. **Cloner le dépôt** dans le répertoire web de votre serveur local :

```bash
git clone https://github.com/benkaliayoub1-cmyk/restaurant_universitaire.git
cd restaurant_universitaire
```

> Avec XAMPP, placez le dossier dans `htdocs/` ; avec Laragon, dans `www/`.

2. **Importer la base de données** :

```bash
mysql -u root -p < restauration_schema.sql
```

Ou via **phpMyAdmin** : créez la base `resto_univer` puis importez le fichier `restauration_schema.sql`.

3. **Configurer la connexion** dans `config/` (fichier de configuration DB) en renseignant vos identifiants MySQL :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'resto_univer');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. **Générer les comptes de test** en accédant à :

```
http://localhost/restaurant_universitaire/setup.php
```

5. **Accéder à l'application** :

```
http://localhost/restaurant_universitaire/
```

---

## 🗄️ Configuration de la base de données

Le fichier `restauration_schema.sql` crée automatiquement :

- La base de données `resto_univer`
- Toutes les tables avec leurs relations
- **22 plats tunisiens** préconfigurés (entrées, plats, desserts, boissons)
- Les index de performance

> ⚠️ Les mots de passe des comptes de test sont générés via `setup.php` avec un vrai hash **bcrypt**. Ne pas utiliser les hashes présents dans le fichier SQL en production.

---

## 👥 Rôles utilisateurs

| Rôle | Fonctionnalités principales |
|------|-----------------------------|
| **Étudiant** | Connexion, consultation du menu, achat de ticket, affichage du QR code, gestion du solde D17 |
| **Caissier** | Connexion, scan/validation des QR codes des tickets |
| **Admin** | Gestion des menus, des repas, des utilisateurs, tableau de bord |

---

## 🗃️ Structure de la base de données

```
utilisateur          ← table parent (id, nom, email, motDePasse)
  ├── etudiant       ← (matricule, soldeD17)
  ├── admin
  └── caissier       ← (postId)

repas                ← (nom, prix, categorie)
menu                 ← (dateMenu, typeMenu)
menu_repas           ← table de jointure menu ↔ repas

ticket               ← (qrCode, status, montantTotal, id_etudiant, id_menu)
validation_ticket    ← (id_ticket, id_caissier, dateValidation)
```

---

## 📄 Licence

Ce projet est développé dans un cadre académique. Tous droits réservés à l'auteur.
