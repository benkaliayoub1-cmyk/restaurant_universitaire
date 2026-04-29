# 🍽️ Resto ESEN — Système de Restauration Universitaire

**Projet Intégré 2BI · ESEN · Université de La Manouba · 2025/2026**

---

## 📁 Architecture du projet

```
resto_univer/
├── config/
│   ├── database.php     ← Connexion PDO (modifiez DB_USER/DB_PASS)
│   ├── constants.php    ← MENU_PRIX_FIXE=0.200, rôles, statuts
│   └── helpers.php      ← redirect(), flash(), formatPrix(), etc.
│
├── controllers/
│   ├── AuthController.php    ← login, register, logout
│   ├── MenuController.php    ← creerMenu, gererRepas
│   ├── TicketController.php  ← reserver, scanner
│   └── UserController.php    ← updateProfile, updatePassword, CRUD admin
│
├── models/
│   ├── UserModel.php    ← Utilisateurs, étudiants, admin, caissiers
│   ├── MenuModel.php    ← Menus, repas (prix individuel non affiché)
│   └── TicketModel.php  ← Tickets, validation, stats (100% PHP)
│
├── public/
│   ├── css/style.css    ← Bootstrap 5 + thème terracotta
│   └── js/app.js        ← Carousel, QR canvas, show/hide password
│
├── views/
│   ├── layout.php       ← Navbar Bootstrap (partagée par tous)
│   ├── footer.php       ← Pied de page + scripts JS
│   ├── sidebar.php      ← Sidebar dynamique (un seul fichier, tous rôles)
│   ├── partials/
│   │   ├── menu_card.php  ← Carte menu réutilisable (sans prix individuels)
│   │   └── alerts.php     ← Flash messages Bootstrap
│   ├── public/            ← home, menu, historique, login, register
│   ├── student/           ← dashboard, reservation, tickets, ticket_view, profile
│   ├── admin/             ← dashboard, menu, repas, tickets, users, profile
│   └── cashier/           ← scan, history
│
├── index.php            ← Front Controller + bootstrap (require_once universel)
├── .htaccess            ← Protections + RewriteRule
├── setup.php            ← Installateur (SUPPRIMER après usage)
└── restauration_schema.sql ← Tables + données initiales uniquement
```

---

## 🚀 Installation (5 étapes)

### 1. Copier le projet
```
XAMPP : C:\xampp\htdocs\resto_univer\
WAMP  : C:\wamp64\www\resto_univer\
```

### 2. Créer la base de données
1. Démarrez Apache + MySQL via XAMPP/WAMP
2. Ouvrez `http://localhost/phpmyadmin`
3. **Importer** → sélectionnez `restauration_schema.sql` → **Exécuter**

### 3. Configurer la connexion BD
Ouvrez `config/database.php` :
```php
define('DB_USER', 'root');
define('DB_PASS', '');   // votre mot de passe MySQL
```

### 4. Installer les données de test
```
http://localhost/resto_univer/setup.php
```
→ Puis **supprimez `setup.php`** !

### 5. Accéder au site
```
http://localhost/resto_univer/views/public/home.php
```

---

## 👤 Comptes de test

| Rôle      | Email               | Mot de passe  |
|-----------|---------------------|---------------|
| Admin     | admin@esen.tn       | admin123      |
| Caissier  | caissier@esen.tn    | caissier123   |
| Étudiant  | ahmed@esen.tn       | etudiant123   |

---

## 🔑 Points techniques

| Sujet | Solution |
|---|---|
| **APP_ROOT already defined** | `index.php` utilise `dirname(__FILE__)` — jamais de `define()` répété |
| **Trigger MySQL 1442** | Expiration tickets gérée en PHP : `TicketModel::expirerTickets()` |
| **Prix fixe 0.200 DT** | `MENU_PRIX_FIXE` dans `constants.php`, prix repas stocké à 0 en BD |
| **Prix individuels cachés** | `views/partials/menu_card.php` n'affiche que le prix fixe total |
| **Sidebar unique** | `views/sidebar.php` génère les liens selon `$_SESSION['role']` |
| **Show/Hide password** | Bouton `.pwd-toggle` + `app.js::initPasswordToggle()` |

