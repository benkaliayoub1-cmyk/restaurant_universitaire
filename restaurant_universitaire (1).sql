-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 04 mai 2026 à 11:16
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `restaurant_universitaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Structure de la table `caissier`
--

CREATE TABLE `caissier` (
  `id` int(11) NOT NULL,
  `postId` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `caissier`
--

INSERT INTO `caissier` (`id`, `postId`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `id` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `soldeD17` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`id`, `matricule`, `soldeD17`) VALUES
(3, '20240001', 0),
(4, '123456789', 0),
(5, '11554422', 0),
(6, '11448855', 0),
(7, '5555555', 0),
(8, '558456', 0),
(9, '41254125', 0),
(10, '4455662', 0),
(11, '45455115', 0);

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `dateMenu` date NOT NULL,
  `typeMenu` enum('petit-dejeuner','dejeuner','diner') NOT NULL DEFAULT 'dejeuner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `menu`
--

INSERT INTO `menu` (`id_menu`, `dateMenu`, `typeMenu`) VALUES
(1, '2026-04-28', 'dejeuner'),
(2, '2026-04-29', 'diner'),
(3, '2026-04-30', 'dejeuner'),
(4, '2026-05-01', 'diner'),
(5, '2026-05-02', 'dejeuner'),
(6, '2026-05-03', 'diner');

-- --------------------------------------------------------

--
-- Structure de la table `menu_repas`
--

CREATE TABLE `menu_repas` (
  `id_menu` int(11) NOT NULL,
  `id_repas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `menu_repas`
--

INSERT INTO `menu_repas` (`id_menu`, `id_repas`) VALUES
(1, 1),
(1, 6),
(1, 14),
(1, 19),
(2, 4),
(2, 18),
(2, 22),
(2, 23),
(3, 2),
(3, 8),
(3, 10),
(3, 18),
(4, 4),
(4, 11),
(4, 17),
(4, 22),
(5, 4),
(5, 8),
(5, 18),
(5, 22),
(5, 23),
(6, 2),
(6, 7),
(6, 18),
(6, 19);

-- --------------------------------------------------------

--
-- Structure de la table `repas`
--

CREATE TABLE `repas` (
  `id_repas` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prix` float NOT NULL DEFAULT 0,
  `categorie` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `repas`
--

INSERT INTO `repas` (`id_repas`, `nom`, `prix`, `categorie`) VALUES
(1, 'Brik à l\'œuf', 0, 'entree'),
(2, 'Salade méchouia', 0, 'entree'),
(3, 'Salade tunisienne', 0, 'entree'),
(4, 'Chorba (soupe)', 0, 'entree'),
(5, 'Lablebi', 0, 'entree'),
(6, 'Couscous agneau', 0, 'plat'),
(7, 'Couscous poulet', 0, 'plat'),
(8, 'Tajine tunisien', 0, 'plat'),
(9, 'Kafteji', 0, 'plat'),
(10, 'Poulet grillé + riz', 0, 'plat'),
(11, 'Merguez + frites', 0, 'plat'),
(12, 'Ojja (chakchouka)', 0, 'plat'),
(13, 'Makloub (riz farci)', 0, 'plat'),
(14, 'Assida zgougou', 0, 'dessert'),
(15, 'Makroudh', 0, 'dessert'),
(16, 'Bambalouni', 0, 'dessert'),
(17, 'Fruit de saison', 0, 'dessert'),
(18, 'Yaourt nature', 0, 'dessert'),
(19, 'Eau minérale 50cl', 0, 'boisson'),
(20, 'Jus d\'orange frais', 0, 'boisson'),
(21, 'Thé à la menthe', 0, 'boisson'),
(22, 'Lben', 0, 'boisson'),
(23, 'ma9rouna', 0, 'plat'),
(24, 'riz au fruit secs', 0, 'plat'),
(25, 'ojja mergez', 0, 'plat');

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

CREATE TABLE `ticket` (
  `id_ticket` int(11) NOT NULL,
  `dateAchat` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('valide','utilise','annule') NOT NULL DEFAULT 'valide',
  `qrCode` varchar(255) NOT NULL,
  `montantTotal` float NOT NULL DEFAULT 0.2,
  `transactionD17` varchar(255) DEFAULT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ticket`
--

INSERT INTO `ticket` (`id_ticket`, `dateAchat`, `status`, `qrCode`, `montantTotal`, `transactionD17`, `id_etudiant`, `id_menu`) VALUES
(1, '2026-04-28 06:36:32', 'utilise', 'B020B650E7B7FB1F-3-1777354592', 0.2, 'D17-SIM-50583592', 3, 1),
(2, '2026-04-29 09:51:36', 'utilise', 'AC10769FD98440D4-3-1777452696', 0.2, 'D17-SIM-5D20FE22', 3, 2),
(3, '2026-04-29 10:58:42', 'annule', 'E32EEFB6AE633376-4-1777456722', 0.2, NULL, 4, 2),
(4, '2026-04-29 11:19:45', 'utilise', 'D40C1ACC64596DEB-5-1777457985', 0.2, NULL, 5, 2),
(5, '2026-04-29 20:16:02', 'utilise', 'CDA496122CA22D05-6-1777490162', 0.2, NULL, 6, 2),
(6, '2026-04-29 21:10:03', 'utilise', '3A11AA3127D5F772-7-1777493403', 0.2, 'D17-SIM-777EEC57', 7, 2),
(7, '2026-04-30 11:57:23', 'utilise', '0F8EFD226F5EAC16-4-1777546643', 0.2, 'D17-SIM-C7972601', 4, 3),
(8, '2026-04-30 11:59:48', 'annule', '60B6FBBDA56CD7FC-3-1777546788', 0.2, NULL, 3, 3),
(9, '2026-04-30 12:10:38', 'utilise', 'C475A1B5FEA7B5F8-8-1777547438', 0.2, 'D17-SIM-0F0F7E91', 8, 3),
(10, '2026-04-30 15:44:22', 'utilise', '849DBCF0F129D189-6-1777560262', 0.2, NULL, 6, 3),
(11, '2026-04-30 15:45:11', 'annule', 'FCDED73E7EFC65B5-5-1777560311', 0.2, NULL, 5, 3),
(12, '2026-04-30 15:45:33', 'utilise', 'F8152B729F43B6C3-7-1777560333', 0.2, 'D17-SIM-3DBE1781', 7, 3),
(13, '2026-05-01 10:41:34', 'utilise', '6584A2063A8AE021-4-1777628494', 0.2, NULL, 4, 4),
(14, '2026-05-01 10:42:06', 'annule', '96607592C6922E1A-5-1777628526', 0.2, NULL, 5, 4),
(15, '2026-05-01 10:42:29', 'utilise', '40EF7CA3B79C5A65-6-1777628549', 0.2, NULL, 6, 4),
(16, '2026-05-01 10:57:23', 'annule', 'EDAED417CA80A575-8-1777629443', 0.2, NULL, 8, 4),
(17, '2026-05-01 21:37:53', 'annule', '87665F566F7D2F7B-3-1777667873', 0.2, NULL, 3, 4),
(18, '2026-05-02 09:56:58', 'utilise', '1559D4C3CEE1832A-5-1777712218', 0.2, NULL, 5, 5),
(19, '2026-05-02 09:57:47', 'utilise', '0E0BB45FE89698AA-4-1777712267', 0.2, 'D17-SIM-A15E4437', 4, 5),
(20, '2026-05-02 09:58:33', 'annule', '239F984AEB824D5B-7-1777712313', 0.2, NULL, 7, 5),
(21, '2026-05-02 11:27:29', 'utilise', 'A7534BBCE39A6D44-9-1777717649', 0.2, 'D17-SIM-1D307A6C', 9, 5),
(22, '2026-05-02 18:26:13', 'annule', '7412C237F018D62D-10-1777742773', 0.2, NULL, 10, 5),
(23, '2026-05-02 22:46:05', 'utilise', '8669C5BC8169391A-11-1777758365', 0.2, NULL, 11, 5),
(24, '2026-05-03 19:11:45', 'utilise', '23FAC9BB30ED93C6-4-1777831905', 0.2, NULL, 4, 6),
(25, '2026-05-03 19:12:24', 'utilise', 'DCD750AAC4829CBA-5-1777831944', 0.2, NULL, 5, 6),
(26, '2026-05-03 19:13:44', 'valide', 'DFE9D796E783B4C0-6-1777832024', 0.2, NULL, 6, 6);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `motDePasse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `motDePasse`) VALUES
(1, 'Admin Système', 'admin@esen.tn', '$2y$10$ZtzbpyWQVSyJFHBP0DxQ/uAXTl43pCPtoG2O97GArGNRk6Riw.GG2'),
(2, 'Caissier Principal', 'caissier@esen.tn', '$2y$10$nj6pAHgK2ZeoKFk1kWyS4OmKpLJmTRJw2uUc4q2nvxBlRBF7aVVX2'),
(3, 'Ahmed Ben Salah', 'ahmed@esen.tn', '$2y$10$MwA35dz0umWwySKRMpsHDueHmw.AZP23i08nm5uhKv21fdk3SAXRa'),
(4, 'ayoub', 'ayoub@gmail.com', '$2y$10$yW51MMfJuVfZcAjMBlbRG.2hoxe5CqHtgnE9oTkreWO56Rc.S3M9W'),
(5, 'hamza', 'hamza@esen.tn', '$2y$10$wan5wIbtjBymyKFhSJHoKOkECHaXGxw8C7OfyUZSnLtlKahBJCdsW'),
(6, 'hamma', 'hamma@esen.tn', '$2y$10$jyGNSO9MSgqik1aPdQg8s.e0abATY1Z.Ycpj9AossvuXoYQ79BRNe'),
(7, 'khaled', 'khaled@esen.tn', '$2y$10$zDbaLHuBNYDoXXUHWwKEs.18eR7wuSNOlQgJeGaiC/wgYkz9fR0jm'),
(8, 'chahdoura', 'chahdoura@esen.tn', '$2y$10$INDIEa.wGTYfsBVhDQxH7.g7AIfc.xIPxFjDZ11uipTzNFo1IrvJW'),
(9, 'youssef', 'youssef@esen.tn', '$2y$10$ogNW6BUNLFsJHwDi0w43LOatrYjIepQlVSeKRH0yOvtW/zwFkmxoS'),
(10, 'haithem', 'haithem@esen.tn', '$2y$10$M/6lwEWnZKR7Xkz963u0T.10tpTUS6RERh3aDZTobpW3ZIC66l70G'),
(11, 'salah', 'salah@esen.tn', '$2y$10$Ca0PFFoE6dMmYcJe98GzxuTKjY1.HJCoKKMPDbIcPsAV9TYeXp6Ky');

-- --------------------------------------------------------

--
-- Structure de la table `validation_ticket`
--

CREATE TABLE `validation_ticket` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_caissier` int(11) NOT NULL,
  `dateValidation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `validation_ticket`
--

INSERT INTO `validation_ticket` (`id`, `id_ticket`, `id_caissier`, `dateValidation`) VALUES
(1, 1, 2, '2026-04-28 06:37:40'),
(2, 2, 2, '2026-04-29 09:52:45'),
(3, 4, 2, '2026-04-29 11:20:11'),
(4, 5, 2, '2026-04-29 20:21:16'),
(5, 6, 2, '2026-04-29 21:12:39'),
(6, 7, 2, '2026-04-30 11:58:42'),
(7, 9, 2, '2026-04-30 12:11:48'),
(8, 10, 2, '2026-04-30 15:44:42'),
(9, 12, 2, '2026-04-30 15:45:54'),
(10, 13, 2, '2026-05-01 10:41:50'),
(11, 15, 2, '2026-05-01 10:42:50'),
(12, 18, 2, '2026-05-02 09:57:19'),
(13, 19, 2, '2026-05-02 09:58:10'),
(14, 21, 2, '2026-05-02 11:28:00'),
(15, 23, 2, '2026-05-02 22:47:34'),
(16, 24, 2, '2026-05-03 19:12:02'),
(17, 25, 2, '2026-05-03 19:12:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `caissier`
--
ALTER TABLE `caissier`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD UNIQUE KEY `dateMenu` (`dateMenu`),
  ADD KEY `idx_menu_date` (`dateMenu`);

--
-- Index pour la table `menu_repas`
--
ALTER TABLE `menu_repas`
  ADD PRIMARY KEY (`id_menu`,`id_repas`),
  ADD KEY `fk_mr_repas` (`id_repas`);

--
-- Index pour la table `repas`
--
ALTER TABLE `repas`
  ADD PRIMARY KEY (`id_repas`);

--
-- Index pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id_ticket`),
  ADD UNIQUE KEY `qrCode` (`qrCode`),
  ADD KEY `idx_ticket_etudiant` (`id_etudiant`),
  ADD KEY `idx_ticket_menu` (`id_menu`),
  ADD KEY `idx_ticket_status` (`status`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `validation_ticket`
--
ALTER TABLE `validation_ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_val_ticket` (`id_ticket`),
  ADD KEY `fk_val_caissier` (`id_caissier`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `repas`
--
ALTER TABLE `repas`
  MODIFY `id_repas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `validation_ticket`
--
ALTER TABLE `validation_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `caissier`
--
ALTER TABLE `caissier`
  ADD CONSTRAINT `fk_caissier_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD CONSTRAINT `fk_etudiant_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_repas`
--
ALTER TABLE `menu_repas`
  ADD CONSTRAINT `fk_mr_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mr_repas` FOREIGN KEY (`id_repas`) REFERENCES `repas` (`id_repas`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiant` (`id`),
  ADD CONSTRAINT `fk_ticket_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

--
-- Contraintes pour la table `validation_ticket`
--
ALTER TABLE `validation_ticket`
  ADD CONSTRAINT `fk_val_caissier` FOREIGN KEY (`id_caissier`) REFERENCES `caissier` (`id`),
  ADD CONSTRAINT `fk_val_ticket` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id_ticket`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
