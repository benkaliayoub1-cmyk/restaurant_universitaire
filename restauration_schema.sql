-- ============================================================
--  Système de Restauration Numérique — Schéma SQL
--  Base de données : resto_univer
--  Contenu : CREATE TABLE + INSERT données initiales uniquement
-- ============================================================

CREATE DATABASE IF NOT EXISTS resto_univer
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE resto_univer;

-- ============================================================
--  TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS utilisateur (
    id          INT           PRIMARY KEY AUTO_INCREMENT,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    motDePasse  VARCHAR(255)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS etudiant (
    id          INT          PRIMARY KEY,
    matricule   VARCHAR(50)  NOT NULL UNIQUE,
    soldeD17    FLOAT        NOT NULL DEFAULT 0,
    CONSTRAINT fk_etudiant_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY,
    CONSTRAINT fk_admin_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS caissier (
    id      INT PRIMARY KEY,
    postId  INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_caissier_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note : prix stocké à 0, le prix affiché est toujours 0.200 DT (MENU_PRIX_FIXE)
CREATE TABLE IF NOT EXISTS repas (
    id_repas   INT          PRIMARY KEY AUTO_INCREMENT,
    nom        VARCHAR(150) NOT NULL,
    prix       FLOAT        NOT NULL DEFAULT 0,
    categorie  VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu (
    id_menu   INT  PRIMARY KEY AUTO_INCREMENT,
    dateMenu  DATE NOT NULL UNIQUE,
    typeMenu  ENUM('petit-dejeuner','dejeuner','diner') NOT NULL DEFAULT 'dejeuner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu_repas (
    id_menu   INT NOT NULL,
    id_repas  INT NOT NULL,
    PRIMARY KEY (id_menu, id_repas),
    CONSTRAINT fk_mr_menu  FOREIGN KEY (id_menu)  REFERENCES menu(id_menu)  ON DELETE CASCADE,
    CONSTRAINT fk_mr_repas FOREIGN KEY (id_repas) REFERENCES repas(id_repas) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ticket (
    id_ticket       INT           PRIMARY KEY AUTO_INCREMENT,
    dateAchat       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status          ENUM('valide','utilise','annule') NOT NULL DEFAULT 'valide',
    qrCode          VARCHAR(255)  NOT NULL UNIQUE,
    montantTotal    FLOAT         NOT NULL DEFAULT 0.200,
    transactionD17  VARCHAR(255)  NULL,
    id_etudiant     INT           NOT NULL,
    id_menu         INT           NOT NULL,
    CONSTRAINT fk_ticket_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant(id)  ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_menu     FOREIGN KEY (id_menu)     REFERENCES menu(id_menu) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS validation_ticket (
    id              INT      PRIMARY KEY AUTO_INCREMENT,
    id_ticket       INT      NOT NULL,
    id_caissier     INT      NOT NULL,
    dateValidation  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_val_ticket   FOREIGN KEY (id_ticket)   REFERENCES ticket(id_ticket) ON DELETE CASCADE,
    CONSTRAINT fk_val_caissier FOREIGN KEY (id_caissier) REFERENCES caissier(id)       ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  INDEX
-- ============================================================
CREATE INDEX IF NOT EXISTS idx_ticket_etudiant ON ticket(id_etudiant);
CREATE INDEX IF NOT EXISTS idx_ticket_menu     ON ticket(id_menu);
CREATE INDEX IF NOT EXISTS idx_ticket_status   ON ticket(status);
CREATE INDEX IF NOT EXISTS idx_menu_date       ON menu(dateMenu);

-- ============================================================
--  DONNÉES INITIALES
--  Comptes de test — exécutez setup.php pour les créer avec
--  de vrais hashes bcrypt (les hashes ci-dessous sont pour
--  le mot de passe "password" uniquement à titre d'exemple)
--  URL : http://localhost/resto_univer/setup.php
-- ============================================================

-- Plats tunisiens initiaux
INSERT IGNORE INTO repas (id_repas, nom, prix, categorie) VALUES
-- Entrées
(1,  'Brik à l\'œuf',           0, 'entree'),
(2,  'Salade méchouia',         0, 'entree'),
(3,  'Salade tunisienne',       0, 'entree'),
(4,  'Chorba (soupe)',          0, 'entree'),
(5,  'Lablebi (soupe pois)',    0, 'entree'),
-- Plats principaux
(6,  'Couscous agneau',         0, 'plat'),
(7,  'Couscous poulet',         0, 'plat'),
(8,  'Tajine tunisien',         0, 'plat'),
(9,  'Kafteji',                 0, 'plat'),
(10, 'Poulet grillé + riz',     0, 'plat'),
(11, 'Merguez + frites',        0, 'plat'),
(12, 'Ojja (chakchouka)',       0, 'plat'),
(13, 'Makloub (riz farci)',     0, 'plat'),
-- Desserts
(14, 'Assida zgougou',          0, 'dessert'),
(15, 'Makroudh',                0, 'dessert'),
(16, 'Bambalouni',              0, 'dessert'),
(17, 'Fruit de saison',         0, 'dessert'),
(18, 'Yaourt nature',           0, 'dessert'),
-- Boissons
(19, 'Eau minérale 50cl',       0, 'boisson'),
(20, 'Jus d\'orange frais',     0, 'boisson'),
(21, 'Thé à la menthe',         0, 'boisson'),
(22, 'Lben (babeurre)',         0, 'boisson');

-- ============================================================
--  COMPTES DE TEST
--  Les hashes sont générés par setup.php (bcrypt réel)
--  Exécutez http://localhost/resto_univer/setup.php
-- ============================================================
