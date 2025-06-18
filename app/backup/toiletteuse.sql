-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 04 juin 2025 à 15:10
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `toiletteuse`
--

-- --------------------------------------------------------

--
-- Structure de la table `animal`
--

DROP TABLE IF EXISTS `animal`;
CREATE TABLE IF NOT EXISTS `animal` (
  `id_animal` int NOT NULL AUTO_INCREMENT,
  `id_client` int DEFAULT NULL,
  `nom_animal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `race` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `poids` decimal(5,2) DEFAULT NULL,
  `taille` decimal(5,2) DEFAULT NULL,
  `remarques` text COLLATE utf8mb4_general_ci,
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_animal`),
  KEY `animal_1` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `animal`
--

INSERT INTO `animal` (`id_animal`, `id_client`, `nom_animal`, `type`, `race`, `date_de_naissance`, `poids`, `taille`, `remarques`, `visible`) VALUES
(6, 4, 'Mochi', 'Chien', 'Shibainu', '2024-08-10', 15.00, 90.00, '', 1),
(7, NULL, 'Mocha', 'Chat', 'Sacrée de Birmanie', '2024-02-22', 7.00, 67.00, '', 1),
(8, 4, 'Zechia', 'Chat', 'Sacrée de Birmanie', '2024-03-04', 8.00, 54.00, '', 1),
(9, 4, 'Ato', 'Chat', 'British Shorthair', '2024-01-19', 8.00, 68.00, 'Rien a signalé\r\n', 1),
(10, NULL, 'Machiato', 'Chat', 'Ragdoll', '2024-03-08', 8.00, 75.00, '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code_postal` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ville` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation_client` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `telephone`, `email`, `adresse`, `code_postal`, `ville`, `date_creation_client`) VALUES
(4, 'Wang', 'Lee', '0606060606', 'lee@gmail.com', 'Allé des Arbres', '18000', 'Bourges', '2025-05-13 14:33:01');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `id_rdv` int NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `type_paiement` enum('Espèce','Carte') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en attente','payé','remboursé') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `id_intervention` (`id_rdv`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `id_rdv`, `montant`, `type_paiement`, `date_paiement`, `statut`) VALUES
(28, 6, 15.00, 'Carte', '2025-05-14 00:00:00', 'payé'),
(29, 8, 5.00, 'Carte', '2025-05-15 00:00:00', 'payé'),
(30, 7, 65.00, 'Espèce', '2025-05-15 00:00:00', 'payé'),
(31, 10, 5.00, 'Espèce', '2025-05-19 00:00:00', 'payé'),
(32, 9, 65.00, 'Carte', '2025-06-03 00:00:00', 'payé'),
(33, 13, 15.00, 'Carte', '2025-06-03 00:00:00', 'payé'),
(34, 15, 55.00, 'Espèce', '2025-06-03 00:00:00', 'payé'),
(35, 16, 65.00, 'Carte', '2025-06-03 00:00:00', 'payé'),
(36, 20, 15.00, 'Carte', '2025-06-04 00:00:00', 'payé');

-- --------------------------------------------------------

--
-- Structure de la table `prestations`
--

DROP TABLE IF EXISTS `prestations`;
CREATE TABLE IF NOT EXISTS `prestations` (
  `id_prestation` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tarif` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id_prestation`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prestations`
--

INSERT INTO `prestations` (`id_prestation`, `nom`, `tarif`) VALUES
(6, 'Tonte (Chien moyenne taille)', 55.00),
(7, 'Tonte (Chien grande taille)', 65.00),
(8, 'Griffes', 5.00),
(9, 'Finitions pattes, griffes, oreilles chiens', 15.00),
(10, 'Épilation (Chien petite taille)', 55.00);

-- --------------------------------------------------------

--
-- Structure de la table `rdv`
--

DROP TABLE IF EXISTS `rdv`;
CREATE TABLE IF NOT EXISTS `rdv` (
  `id_rdv` int NOT NULL AUTO_INCREMENT,
  `id_animal` int NOT NULL,
  `id_prestation` int NOT NULL,
  `date_heure` datetime NOT NULL,
  `remarque` text COLLATE utf8mb4_general_ci,
  `statut` enum('prévu','annulé','réalisé') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_rdv`),
  KEY `id_chien` (`id_animal`),
  KEY `id_prestation` (`id_prestation`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rdv`
--

INSERT INTO `rdv` (`id_rdv`, `id_animal`, `id_prestation`, `date_heure`, `remarque`, `statut`) VALUES
(6, 6, 9, '2025-05-14 15:25:00', '', 'réalisé'),
(7, 7, 7, '2025-05-23 15:00:00', 'Attention aux poils', 'réalisé'),
(8, 7, 8, '2025-05-14 15:00:00', '', 'réalisé'),
(9, 9, 7, '2025-05-16 13:05:00', '', 'réalisé'),
(10, 6, 8, '2025-05-21 17:10:00', '', 'réalisé'),
(13, 8, 9, '2025-05-19 18:00:00', '', 'réalisé'),
(14, 6, 9, '2025-06-04 17:45:00', '', 'prévu'),
(15, 6, 10, '2025-06-04 17:50:00', '', 'réalisé'),
(16, 8, 7, '2025-06-06 10:00:00', '', 'réalisé'),
(17, 10, 8, '2025-06-05 10:35:00', '', 'réalisé'),
(18, 7, 6, '2025-05-27 11:45:00', '', ''),
(19, 10, 9, '2025-05-26 12:45:00', '', 'réalisé'),
(20, 10, 9, '2025-06-07 12:45:00', '', 'réalisé'),
(21, 6, 10, '2025-10-28 08:15:00', '', 'prévu');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom_utilisateur`, `mot_de_passe`) VALUES
(3, 'toiletteuse', '$2y$10$6sFwVdiwftkbkS/wFh0PnunRMXb8/rer6bdjPl0Ot8TmWx4uWXizm');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE SET NULL ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
