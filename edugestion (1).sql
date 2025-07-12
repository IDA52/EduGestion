-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 02 juil. 2025 à 17:02
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
-- Base de données : `edugestion`
--

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE `absences` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `date_absence` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `justifiee` tinyint(1) NOT NULL DEFAULT 0,
  `justificatif` text DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `assignations`
--

CREATE TABLE `assignations` (
  `id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `heures_attribuees` int(11) NOT NULL DEFAULT 0,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('En cours','Terminé','Annulé') NOT NULL DEFAULT 'En cours',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `assignations`
--

INSERT INTO `assignations` (`id`, `enseignant_id`, `module_id`, `annee_academique`, `semestre`, `heures_attribuees`, `date_debut`, `date_fin`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-2024', 'S3', 60, NULL, NULL, 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(2, 2, 2, '2023-2024', 'S3', 30, NULL, NULL, 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(3, 3, 3, '2023-2024', 'S7', 40, NULL, NULL, 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30');

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `niveau_id` int(11) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `annee_scolaire` varchar(9) DEFAULT NULL,
  `niveau` enum('L1','L2','L3','M1','M2','D') NOT NULL,
  `filiere` varchar(100) NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `niveau_id`, `capacite`, `description`, `annee_scolaire`, `niveau`, `filiere`, `annee_academique`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Classe Test', NULL, NULL, NULL, NULL, 'L1', '', '', 1, '2025-06-26 15:11:41', '2025-06-26 15:11:41'),
(2, 'anphi 1', 1, 2000, 'CE CLASSE EST RÉSERVÉ  AUX ETUDIANTS DE LA LICENCE 1 EN MANAGEMENT INFORMATISER DES ORGASATION', '2025', 'L1', '', '', 1, '2025-06-27 17:39:29', '2025-06-27 17:39:29'),
(3, 'a1', 2, NULL, '', '2025', 'L1', '', '', 1, '2025-06-27 21:51:21', '2025-06-27 21:51:21');

-- --------------------------------------------------------

--
-- Structure de la table `creneaux_horaire`
--

CREATE TABLE `creneaux_horaire` (
  `id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `matiere_id` int(11) DEFAULT NULL,
  `jour` enum('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `salle` varchar(50) NOT NULL,
  `type_cours` enum('Cours','TD','TP') NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `creneaux_horaire`
--

INSERT INTO `creneaux_horaire` (`id`, `classe_id`, `enseignant_id`, `matiere_id`, `jour`, `heure_debut`, `heure_fin`, `salle`, `type_cours`, `actif`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 1, 'Lundi', '22:02:00', '23:04:00', '23', 'Cours', 1, '2025-06-27 20:04:33', '2025-06-27 20:04:33'),
(2, 2, 8, 1, 'Lundi', '22:02:00', '23:04:00', '23', 'Cours', 1, '2025-06-27 20:04:53', '2025-06-27 20:04:53'),
(3, 2, 1, 3, 'Lundi', '00:13:00', '01:13:00', '23', 'Cours', 1, '2025-06-27 20:13:39', '2025-06-27 20:13:39');

-- --------------------------------------------------------

--
-- Structure de la table `deliberations`
--

CREATE TABLE `deliberations` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `moyenne_semestre` decimal(4,2) NOT NULL,
  `credits_obtenus` int(11) NOT NULL DEFAULT 0,
  `credits_totaux` int(11) NOT NULL DEFAULT 0,
  `decision` enum('Admis','Admis avec conditions','Redoublement','Exclusion') NOT NULL,
  `mention` enum('Passable','Assez bien','Bien','Très bien','Excellent') DEFAULT NULL,
  `date_deliberation` date NOT NULL,
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `emplois_du_temps`
--

CREATE TABLE `emplois_du_temps` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `jour` enum('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `salle` varchar(50) NOT NULL,
  `type_cours` enum('Cours','TD','TP') NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `classe_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `emplois_du_temps`
--

INSERT INTO `emplois_du_temps` (`id`, `module_id`, `enseignant_id`, `jour`, `heure_debut`, `heure_fin`, `salle`, `type_cours`, `annee_academique`, `semestre`, `actif`, `created_at`, `updated_at`, `classe_id`) VALUES
(1, 1, 1, 'Lundi', '08:00:00', '10:00:00', 'A101', 'Cours', '2023-2024', 'S3', 0, '2025-06-24 22:52:30', '2025-06-30 15:28:48', NULL),
(2, 1, 1, 'Mercredi', '14:00:00', '16:00:00', 'TP101', 'TP', '2023-2024', 'S3', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30', NULL),
(3, 2, 2, 'Mardi', '10:00:00', '12:00:00', 'A102', 'Cours', '2023-2024', 'S3', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30', NULL),
(4, 3, 3, 'Jeudi', '16:00:00', '18:00:00', 'A103', 'Cours', '2023-2024', 'S7', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30', NULL),
(5, 3, 8, 'Mardi', '21:40:00', '21:40:00', '23', 'Cours', '2024-2025', 'S1', 1, '2025-06-27 20:40:40', '2025-06-27 20:40:40', NULL),
(6, 3, 8, 'Mardi', '21:40:00', '21:40:00', '23', 'Cours', '2024-2025', 'S1', 1, '2025-06-27 20:40:51', '2025-06-27 20:40:51', NULL),
(7, 2, 8, 'Mardi', '20:49:00', '21:49:00', '23', 'Cours', '2024-2025', 'S1', 1, '2025-06-27 20:50:11', '2025-06-27 20:50:11', NULL),
(8, 2, 3, 'Mardi', '20:55:00', '22:55:00', '23', 'Cours', '2024-2025', 'S9', 1, '2025-06-27 20:55:45', '2025-06-27 20:55:45', NULL),
(9, 2, 3, 'Mardi', '20:55:00', '22:55:00', '23', 'Cours', '2024-2025', 'S9', 1, '2025-06-27 20:55:57', '2025-06-27 20:55:57', NULL),
(10, 2, 3, 'Mardi', '21:50:00', '23:49:00', '23', 'Cours', '2024-2025', 'S9', 1, '2025-06-27 21:49:55', '2025-06-27 21:49:55', NULL),
(11, 2, 3, 'Mardi', '21:50:00', '23:49:00', '23', 'Cours', '2024-2025', 'S9', 1, '2025-06-27 21:50:04', '2025-06-27 21:50:04', NULL),
(12, 2, 1, 'Lundi', '16:15:00', '16:15:00', '23', 'Cours', '2024-2025', 'S8', 1, '2025-06-30 15:15:48', '2025-06-30 15:15:48', NULL),
(13, 1, 12, 'Vendredi', '19:48:00', '21:48:00', '3', 'TD', '2024-2025', 'S5', 1, '2025-07-01 17:49:34', '2025-07-01 17:49:34', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `specialite` varchar(255) DEFAULT NULL,
  `grade` enum('Professeur','Maître de conférences','Chargé de cours','Assistant') DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `email`, `telephone`, `date_naissance`, `adresse`, `specialite`, `grade`, `date_embauche`, `salaire`, `actif`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'ENS001', 'Dupont', 'Marie', 'marie.dupont@edugestion.com', '+1234567891', NULL, NULL, 'Informatique', 'Professeur', '2020-09-01', NULL, 1, NULL, '2025-06-24 22:52:29', '2025-06-24 22:52:29'),
(2, 'ENS002', 'Bernard', 'Pierre', 'pierre.bernard@edugestion.com', '+1234567893', NULL, NULL, 'Mathématiques', 'Maître de conférences', '2021-03-15', NULL, 0, NULL, '2025-06-24 22:52:29', '2025-06-25 00:37:07'),
(3, 'ENS003', 'Leroy', 'Sophie', 'sophie.leroy@edugestion.com', '+1234567894', NULL, NULL, 'Physique', 'Chargé de cours', '2022-01-10', NULL, 1, NULL, '2025-06-24 22:52:29', '2025-06-24 22:52:29'),
(4, '2025', 'sow', 'Mamadou', 'sow@gmail.com', '77777777777', NULL, 'louga', 'developpeur', 'Chargé de cours', NULL, NULL, 0, NULL, '2025-06-27 16:50:46', '2025-06-27 16:59:07'),
(8, '123', 'sow', 'Mamadou', 'sow@gmail.sn', '77777777777', '3200-02-12', '', 'developpeur', '', '2025-06-27', 999.99, 0, NULL, '2025-06-27 16:58:52', '2025-06-28 16:34:34'),
(12, '2001', 'SOW', 'Mamadou', 'sowsalim01@gmail.com', '', '2001-12-28', '', 'developpeur', '', '2025-07-01', 850000.00, 1, NULL, '2025-07-01 17:17:40', '2025-07-01 17:17:40');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(255) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `niveau` enum('L1','L2','L3','M1','M2','D') NOT NULL,
  `filiere` varchar(255) NOT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `date_inscription` date DEFAULT NULL,
  `moyenne` decimal(4,2) DEFAULT 0.00,
  `credits` int(11) DEFAULT 0,
  `statut` enum('Inscrit','En cours','Diplômé','Abandon') NOT NULL DEFAULT 'Inscrit',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `matricule`, `nom`, `prenom`, `sexe`, `email`, `telephone`, `date_naissance`, `lieu_naissance`, `adresse`, `niveau`, `filiere`, `classe_id`, `annee_academique`, `date_inscription`, `moyenne`, `credits`, `statut`, `actif`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'ETU001', 'Martin', 'Thomas', NULL, 'thomas.martin@student.com', '+1234567895', NULL, NULL, NULL, 'L2', 'Informatique', NULL, '2023-2024', NULL, 13.00, 78, 'Inscrit', 1, NULL, '2025-06-24 22:52:29', '2025-07-01 19:55:51'),
(2, 'ETU002', 'Dubois', 'Emma', NULL, 'emma.dubois@student.com', '+1234567896', NULL, NULL, NULL, 'L3', 'Mathématiques', NULL, '2023-2024', NULL, 16.20, 90, 'Inscrit', 1, NULL, '2025-06-24 22:52:29', '2025-06-24 22:52:29'),
(3, 'ETU003', 'Moreau', 'Lucas', NULL, 'lucas.moreau@student.com', '+1234567897', NULL, NULL, NULL, 'M1', 'Physique', NULL, '2023-2024', NULL, 15.80, 120, 'Inscrit', 1, NULL, '2025-06-24 22:52:29', '2025-06-24 22:52:29'),
(4, '23020100133', 'diop', 'samba', 'M', 'samba.diop3@univ-thies.sn', '779708782', '2344-03-12', NULL, 'waly babacar', 'L1', '', NULL, '', '2025-06-27', 0.00, 0, 'Inscrit', 1, NULL, '2025-06-27 17:11:21', '2025-06-27 17:11:21'),
(5, '33', 'BA', 'souleymane', 'M', 'ba@gmail.com', '888888888', '2000-03-23', NULL, 'waly', 'L1', '', 2, '', '2025-06-27', 12.00, 10, 'Inscrit', 1, NULL, '2025-06-27 17:40:58', '2025-06-30 17:34:23'),
(6, '2324', 'diop', 'boy', 'M', 'samba@gmai.com', '779708782', '2033-12-12', NULL, 'waly babacar', 'L1', '', 3, '', '2025-07-02', 0.00, 0, 'Inscrit', 1, NULL, '2025-07-01 22:25:46', '2025-07-01 22:25:46');

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

CREATE TABLE `evenements` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `type` enum('Examen','Réunion','Conférence','Autre') NOT NULL DEFAULT 'Autre',
  `priorite` enum('Basse','Normale','Haute','Urgente') NOT NULL DEFAULT 'Normale',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`id`, `titre`, `description`, `date`, `heure_debut`, `heure_fin`, `lieu`, `type`, `priorite`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Examen Java', 'Examen final du module Programmation Java', '2023-12-20', '14:00:00', '16:00:00', 'Salle A101', 'Examen', 'Haute', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(2, 'Réunion pédagogique', 'Réunion des enseignants du département informatique', '2023-12-15', '10:00:00', '12:00:00', 'Salle de réunion', 'Réunion', 'Normale', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(3, 'Conférence IA', 'Conférence sur l\'intelligence artificielle', '2023-12-25', '18:00:00', '20:00:00', 'Amphithéâtre', 'Conférence', 'Basse', 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `date_inscription` date NOT NULL,
  `statut` enum('Inscrit','En cours','Validé','Échoué','Abandon') NOT NULL DEFAULT 'Inscrit',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `etudiant_id`, `module_id`, `annee_academique`, `semestre`, `date_inscription`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-2024', 'S3', '2023-09-01', 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(2, 2, 2, '2023-2024', 'S3', '2023-09-01', 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(3, 3, 3, '2023-2024', 'S7', '2023-09-01', 'En cours', '2025-06-24 22:52:30', '2025-06-24 22:52:30');

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-24 23:04:11'),
(2, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:17:07'),
(3, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:19:15'),
(4, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-25 00:21:03'),
(5, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:21:10'),
(6, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:24:06'),
(7, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:26:11'),
(8, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:30:31'),
(9, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:31:43'),
(10, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:31:51'),
(11, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:33:31'),
(12, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:33:35'),
(13, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:34:37'),
(14, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:34:42'),
(15, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:35:29'),
(16, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-25 00:36:39'),
(17, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 00:36:48'),
(18, 1, 'suppression_enseignant', 'Suppression de l\'enseignant ID: 2', '::1', NULL, '2025-06-25 00:37:07'),
(19, 1, 'suppression_utilisateur', 'Suppression de l\'utilisateur ID: 3', '::1', NULL, '2025-06-25 00:37:53'),
(20, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 00:52:39'),
(21, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 01:16:04'),
(22, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-25 01:16:12'),
(23, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 01:16:20'),
(24, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 01:16:29'),
(25, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 01:16:37'),
(26, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 01:19:36'),
(27, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 08:47:53'),
(28, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 14:10:52'),
(29, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 14:11:02'),
(30, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-25 14:11:52'),
(31, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-25 14:12:13'),
(32, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 10:55:40'),
(33, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-26 13:15:23'),
(34, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 13:15:28'),
(35, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-26 13:16:21'),
(36, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 13:16:31'),
(37, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-06-26 13:22:54'),
(38, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 13:27:10'),
(39, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-26 13:34:13'),
(40, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 13:34:19'),
(41, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-06-26 13:34:23'),
(42, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-26 13:34:28'),
(43, 1, 'ajout_matiere', 'Ajout de la matière Mathématiques', '::1', NULL, '2025-06-26 15:10:25'),
(44, 1, 'ajout_matiere', 'Ajout de la matière info', '::1', NULL, '2025-06-27 16:24:11'),
(45, 1, 'ajout_enseignant', 'Ajout de l\'enseignant sow Mamadou', '::1', NULL, '2025-06-27 16:50:46'),
(46, 1, 'ajout_enseignant', 'Ajout de l\'enseignant sow Mamadou', '::1', NULL, '2025-06-27 16:58:52'),
(47, 1, 'suppression_enseignant', 'Suppression de l\'enseignant ID: 4', '::1', NULL, '2025-06-27 16:59:07'),
(48, 1, 'ajout_etudiant', 'Ajout de l\'étudiant diop samba', '::1', NULL, '2025-06-27 17:11:21'),
(49, 1, 'ajout_classe', 'Ajout de la classe anphi 1', '::1', NULL, '2025-06-27 17:39:29'),
(50, 1, 'ajout_etudiant', 'Ajout de l\'étudiant BA souleymane', '::1', NULL, '2025-06-27 17:40:58'),
(51, 1, 'ajout_creneau', 'Ajout d\'un créneau horaire', '::1', NULL, '2025-06-27 20:04:33'),
(52, 1, 'ajout_creneau', 'Ajout d\'un créneau horaire', '::1', NULL, '2025-06-27 20:04:53'),
(53, 1, 'ajout_creneau', 'Ajout d\'un créneau horaire', '::1', NULL, '2025-06-27 20:13:39'),
(54, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 20:40:40'),
(55, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 20:40:51'),
(56, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 20:50:11'),
(57, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 20:55:45'),
(58, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 20:55:57'),
(59, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur sidibe samba', '::1', NULL, '2025-06-27 21:09:09'),
(60, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 21:49:55'),
(61, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-27 21:50:04'),
(62, 1, 'ajout_classe', 'Ajout de la classe a1', '::1', NULL, '2025-06-27 21:51:21'),
(63, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-28 16:33:38'),
(64, 1, 'suppression_enseignant', 'Suppression de l\'enseignant ID: 8', '::1', NULL, '2025-06-28 16:34:34'),
(65, 1, 'suppression_enseignant', 'Suppression de l\'enseignant ID: 8', '::1', NULL, '2025-06-28 16:34:50'),
(66, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-06-30 15:11:46'),
(67, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-06-30 15:15:48'),
(68, 1, 'suppression_emploi', 'Suppression d\'un créneau emploi du temps ID: 1', '::1', NULL, '2025-06-30 15:28:48'),
(69, 1, 'ajout_note', 'Ajout d\'une note pour l\'étudiant ID: 5', '::1', NULL, '2025-06-30 17:32:31'),
(70, 1, 'ajout_note', 'Ajout d\'une note pour l\'étudiant ID: 5', '::1', NULL, '2025-06-30 17:34:23'),
(71, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 15:49:15'),
(72, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 15:49:22'),
(73, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 15:49:49'),
(74, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:12:10'),
(75, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 16:14:11'),
(76, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:15:10'),
(77, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:16:04'),
(78, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:17:21'),
(79, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 16:17:40'),
(80, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:17:49'),
(81, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:23:46'),
(82, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:23:53'),
(83, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:24:00'),
(84, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:24:20'),
(85, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:25:18'),
(86, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:25:27'),
(87, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:25:30'),
(88, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:26:22'),
(89, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 16:37:06'),
(90, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:37:14'),
(91, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 16:38:44'),
(92, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 16:38:53'),
(93, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 17:00:19'),
(94, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:00:35'),
(95, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 17:00:49'),
(96, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:01:29'),
(97, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 17:01:59'),
(98, 2, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:06:55'),
(99, 2, 'deconnexion', 'Déconnexion de l\'utilisateur: Dupont Marie', '::1', NULL, '2025-07-01 17:14:11'),
(100, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:14:21'),
(101, 1, 'ajout_enseignant', 'Ajout de l\'enseignant SOW Mamadou', '::1', NULL, '2025-07-01 17:17:40'),
(102, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur SOW Mamadou', '::1', NULL, '2025-07-01 17:18:43'),
(103, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:20:09'),
(104, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 17:22:10'),
(105, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur Diop Samba', '::1', NULL, '2025-07-01 17:25:16'),
(106, 6, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:26:10'),
(107, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 17:33:34'),
(108, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:34:23'),
(109, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 17:39:38'),
(110, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:39:50'),
(111, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 17:40:22'),
(112, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:40:32'),
(113, 1, 'ajout_matiere', 'Ajout de la matière Base de donnees', '::1', NULL, '2025-07-01 17:47:31'),
(114, 1, 'ajout_emploi', 'Ajout d\'un créneau emploi du temps', '::1', NULL, '2025-07-01 17:49:34'),
(115, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 17:51:20'),
(116, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:51:28'),
(117, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 17:56:58'),
(118, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 17:57:49'),
(119, 5, 'ajout_note', 'Ajout d\'une note pour l\'étudiant ID: 1', '::1', NULL, '2025-07-01 19:54:16'),
(120, 5, 'ajout_note', 'Ajout d\'une note pour l\'étudiant ID: 1', '::1', NULL, '2025-07-01 19:55:52'),
(121, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 20:06:47'),
(122, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 20:06:58'),
(123, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 21:15:48'),
(124, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 21:15:59'),
(125, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 21:18:03'),
(126, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 22:19:57'),
(127, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur martin thomas', '::1', NULL, '2025-07-01 22:23:26'),
(128, 1, 'ajout_etudiant', 'Ajout de l\'étudiant diop boy', '::1', NULL, '2025-07-01 22:25:46'),
(129, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur diop boy', '::1', NULL, '2025-07-01 22:26:38'),
(130, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-01 22:27:05'),
(131, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 22:29:44'),
(132, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-01 23:33:37'),
(133, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-01 23:33:48'),
(134, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur sada ba', '::1', NULL, '2025-07-01 23:34:58'),
(135, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:06:22'),
(136, 1, 'ajout_utilisateur', 'Ajout de l\'utilisateur sey djibi', '::1', NULL, '2025-07-02 00:07:49'),
(137, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-02 00:24:21'),
(138, 10, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:24:58'),
(139, 10, 'deconnexion', 'Déconnexion de l\'utilisateur: sey djibi', '::1', NULL, '2025-07-02 00:36:17'),
(140, 10, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:37:02'),
(141, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:37:18'),
(142, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-02 00:37:30'),
(143, 10, 'deconnexion', 'Déconnexion de l\'utilisateur: sey djibi', '::1', NULL, '2025-07-02 00:38:00'),
(144, 10, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:38:31'),
(145, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:38:47'),
(146, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-02 00:39:35'),
(147, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:39:41'),
(148, 1, 'deconnexion', 'Déconnexion de l\'utilisateur: Admin Principal', '::1', NULL, '2025-07-02 00:40:46'),
(149, 5, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:40:52'),
(150, 5, 'deconnexion', 'Déconnexion de l\'utilisateur: SOW Mamadou', '::1', NULL, '2025-07-02 00:41:26'),
(151, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 00:41:34'),
(152, 10, 'deconnexion', 'Déconnexion de l\'utilisateur: sey djibi', '::1', NULL, '2025-07-02 10:32:22'),
(153, 1, 'connexion', 'Connexion réussie', '::1', NULL, '2025-07-02 10:32:35');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `coefficient` decimal(3,2) NOT NULL DEFAULT 1.00,
  `couleur` varchar(20) DEFAULT '#22c55e',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `code`, `nom`, `description`, `coefficient`, `couleur`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'MAT101', 'Mathématiques', 'Cours de mathématiques de base', 1.00, '#22c55e', 1, '2025-06-26 15:08:56', '2025-06-26 15:08:56'),
(2, 'MAT01', 'Mathématiques', '', 1.00, '#007bff', 1, '2025-06-26 15:10:25', '2025-06-26 15:10:25'),
(3, '1234', 'info', 'ertyjukiop9iuytre', 2.00, '#007bff', 1, '2025-06-27 16:24:11', '2025-06-27 16:24:11'),
(5, 'MAT03', 'Base de donnees', '', 1.00, '#007bff', 1, '2025-07-01 17:47:31', '2025-07-01 17:47:31');

-- --------------------------------------------------------

--
-- Structure de la table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) NOT NULL DEFAULT 3,
  `coefficient` decimal(3,2) NOT NULL DEFAULT 1.00,
  `niveau` enum('L1','L2','L3','M1','M2','D') NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `heures_cours` int(11) NOT NULL DEFAULT 0,
  `heures_td` int(11) NOT NULL DEFAULT 0,
  `heures_tp` int(11) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `modules`
--

INSERT INTO `modules` (`id`, `code`, `nom`, `description`, `credits`, `coefficient`, `niveau`, `semestre`, `heures_cours`, `heures_td`, `heures_tp`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'INFO101', 'Programmation Java', 'Introduction à la programmation orientée objet avec Java', 6, 2.00, 'L2', 'S3', 20, 20, 20, 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(2, 'MATH201', 'Algèbre linéaire', 'Étude des espaces vectoriels et applications linéaires', 5, 1.50, 'L2', 'S3', 15, 15, 0, 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(3, 'PHYS301', 'Mécanique quantique', 'Principes fondamentaux de la mécanique quantique', 4, 1.00, 'M1', 'S7', 25, 10, 5, 1, '2025-06-24 22:52:30', '2025-06-24 22:52:30'),
(4, 'TEST01', 'Module Test', 'Module de test', 3, 1.00, 'L1', 'S1', 10, 10, 10, 1, '2025-06-27 17:59:01', '2025-06-27 17:59:01');

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

CREATE TABLE `niveaux` (
  `id` int(11) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT 1,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id`, `nom`, `ordre`, `actif`) VALUES
(1, 'L1', 1, 1),
(2, 'L2', 2, 1),
(3, 'L3', 3, 1),
(4, 'M1', 4, 1),
(5, 'M2', 5, 1),
(6, 'D', 6, 1);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `matiere_id` int(11) DEFAULT NULL,
  `module_id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `annee_academique` varchar(9) NOT NULL,
  `semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10') NOT NULL,
  `type_evaluation` enum('Contrôle continu','Examen','TP','Projet') NOT NULL,
  `note` decimal(4,2) DEFAULT NULL,
  `coefficient` decimal(3,2) NOT NULL DEFAULT 1.00,
  `date_evaluation` date NOT NULL,
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `etudiant_id`, `matiere_id`, `module_id`, `enseignant_id`, `annee_academique`, `semestre`, `type_evaluation`, `note`, `coefficient`, `date_evaluation`, `commentaire`, `created_at`, `updated_at`) VALUES
(17, 5, NULL, 2, 1, '2025-2026', 'S1', 'Contrôle continu', 12.00, 1.00, '2025-06-30', 'bierenrtjkl', '2025-06-30 17:32:31', '2025-06-30 17:32:31'),
(18, 5, NULL, 2, 1, '2025-2026', 'S1', 'Contrôle continu', 12.00, 1.00, '2025-06-30', 'bierenrtjkl', '2025-06-30 17:34:23', '2025-06-30 17:34:23'),
(19, 1, NULL, 1, 12, '2025-2026', 'S1', 'Examen', 13.00, 1.00, '2025-07-01', 'BIEN', '2025-07-01 19:54:16', '2025-07-01 19:54:16'),
(20, 1, NULL, 1, 12, '2025-2026', 'S1', 'Examen', 13.00, 1.00, '2025-07-01', 'BIEN', '2025-07-01 19:55:51', '2025-07-01 19:55:51');

--
-- Déclencheurs `notes`
--
DELIMITER $$
CREATE TRIGGER `update_student_average` AFTER INSERT ON `notes` FOR EACH ROW BEGIN
    DECLARE total_weighted_notes DECIMAL(10,2);
    DECLARE total_coefficients DECIMAL(10,2);
    DECLARE new_average DECIMAL(4,2);
    
    -- Calculer la moyenne pondérée pour cet étudiant
    SELECT 
        SUM(n.note * n.coefficient * m.coefficient),
        SUM(n.coefficient * m.coefficient)
    INTO total_weighted_notes, total_coefficients
    FROM notes n
    JOIN modules m ON n.module_id = m.id
    WHERE n.etudiant_id = NEW.etudiant_id;
    
    -- Calculer la nouvelle moyenne
    IF total_coefficients > 0 THEN
        SET new_average = total_weighted_notes / total_coefficients;
    ELSE
        SET new_average = 0.00;
    END IF;
    
    -- Mettre à jour la moyenne de l'étudiant
    UPDATE etudiants 
    SET moyenne = new_average 
    WHERE id = NEW.etudiant_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_student_credits` AFTER INSERT ON `notes` FOR EACH ROW BEGIN
    DECLARE module_credits INT;
    DECLARE student_credits INT;
    
    -- Récupérer les crédits du module
    SELECT credits INTO module_credits 
    FROM modules 
    WHERE id = NEW.module_id;
    
    -- Récupérer les crédits actuels de l'étudiant
    SELECT credits INTO student_credits 
    FROM etudiants 
    WHERE id = NEW.etudiant_id;
    
    -- Si la note est >= 10, ajouter les crédits
    IF NEW.note >= 10.00 THEN
        UPDATE etudiants 
        SET credits = student_credits + module_credits 
        WHERE id = NEW.etudiant_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','enseignant','etudiant') NOT NULL DEFAULT 'etudiant',
  `telephone` varchar(20) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `telephone`, `actif`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Principal', 'admin@edugestion.com', '$2y$10$lr1SUFwZ2WZ9kP5OmmCHJOkLS5kaufx3IV/VcJWXa8rPunfhZAw/a', 'admin', '+1234567890', 1, NULL, '2025-06-24 22:52:29', '2025-07-01 16:53:49'),
(2, 'Dupont', 'Marie', 'prof@edugestion.com', '$2y$10$lr1SUFwZ2WZ9kP5OmmCHJOkLS5kaufx3IV/VcJWXa8rPunfhZAw/a', 'enseignant', '+1234567891', 1, NULL, '2025-06-24 22:52:29', '2025-07-01 16:54:02'),
(3, 'Martin', 'Jean', 'personnel@edugestion.com', '$2y$10$lr1SUFwZ2WZ9kP5OmmCHJOkLS5kaufx3IV/VcJWXa8rPunfhZAw/a', '', '+1234567892', 0, NULL, '2025-06-24 22:52:29', '2025-07-02 00:05:44'),
(4, 'sidibe', 'samba', 'ss@gmail.com', '$2y$10$lr1SUFwZ2WZ9kP5OmmCHJOkLS5kaufx3IV/VcJWXa8rPunfhZAw/a', 'enseignant', '77777777777', 1, NULL, '2025-06-27 21:09:09', '2025-07-01 16:54:17'),
(5, 'SOW', 'Mamadou', 'sowsalim01@gmail.com', '$2y$10$TCIAnxrq5MRK8GiZt9NBkepw1qBhrafWEXsQwMxP/Z6LhnjZ36FQy', 'enseignant', '783117994', 1, NULL, '2025-07-01 17:18:43', '2025-07-01 17:18:43'),
(6, 'Diop', 'Samba', 'sambadiop@gmail.com', '$2y$10$MSvaz7LRWJqqbsuyLvQdQu8yPK2yiweIfTPfhLjIYeY3SNem7ZhYG', '', '779708782', 1, NULL, '2025-07-01 17:25:16', '2025-07-01 17:25:16'),
(7, 'martin', 'thomas', 'thomas.martin@student.com', '$2y$10$aZC9cxtr2M4.tkQZT/haPe7OtQd.SL0SI7PDccjwSUMJuzHOcUh8K', 'enseignant', '755555555', 1, NULL, '2025-07-01 22:23:26', '2025-07-01 22:23:26'),
(8, 'diop', 'boy', 'samba@gmai.com', '$2y$10$KgqsjV0pDSFDd2FzU58S1.6TlV..hcsCpDvZu4Mo82XRu95pAiWUu', '', '779708782', 1, NULL, '2025-07-01 22:26:38', '2025-07-01 22:26:38'),
(9, 'sada', 'ba', 'sada@gmail.com', '$2y$10$IcThLrHMrOHAnP7kzzXNye96ZOX0ZYCbrJuvfcMgbMUV3MzWGOGHm', '', '66666666', 1, NULL, '2025-07-01 23:34:58', '2025-07-01 23:34:58'),
(10, 'sey', 'djibi', 'sey@gmail.com', '$2y$10$GGngcY.uLbxJW.pL2jjL1.rxMBWaNO6mO9zIEvQz5gjNsc1PvvBpy', 'etudiant', '333333333', 1, NULL, '2025-07-02 00:07:49', '2025-07-02 00:07:49');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_emplois_temps`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_emplois_temps` (
`jour` enum('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi')
,`heure_debut` time
,`heure_fin` time
,`salle` varchar(50)
,`type_cours` enum('Cours','TD','TP')
,`module_nom` varchar(255)
,`enseignant_nom` varchar(201)
,`annee_academique` varchar(9)
,`semestre` enum('S1','S2','S3','S4','S5','S6','S7','S8','S9','S10')
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_moyennes_modules`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_moyennes_modules` (
`code` varchar(20)
,`module_nom` varchar(255)
,`etudiant_nom` varchar(100)
,`etudiant_prenom` varchar(100)
,`moyenne_module` decimal(8,6)
,`nombre_notes` bigint(21)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_statistiques`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_statistiques` (
`total_etudiants` bigint(21)
,`total_enseignants` bigint(21)
,`total_modules` bigint(21)
,`moyenne_generale` decimal(8,6)
,`etudiants_diplomes` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure de la vue `v_emplois_temps`
--
DROP TABLE IF EXISTS `v_emplois_temps`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_emplois_temps`  AS SELECT `edt`.`jour` AS `jour`, `edt`.`heure_debut` AS `heure_debut`, `edt`.`heure_fin` AS `heure_fin`, `edt`.`salle` AS `salle`, `edt`.`type_cours` AS `type_cours`, `m`.`nom` AS `module_nom`, concat(`ens`.`nom`,' ',`ens`.`prenom`) AS `enseignant_nom`, `edt`.`annee_academique` AS `annee_academique`, `edt`.`semestre` AS `semestre` FROM ((`emplois_du_temps` `edt` join `modules` `m` on(`edt`.`module_id` = `m`.`id`)) join `enseignants` `ens` on(`edt`.`enseignant_id` = `ens`.`id`)) WHERE `edt`.`actif` = 1 ORDER BY field(`edt`.`jour`,'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') ASC, `edt`.`heure_debut` ASC ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_moyennes_modules`
--
DROP TABLE IF EXISTS `v_moyennes_modules`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_moyennes_modules`  AS SELECT `m`.`code` AS `code`, `m`.`nom` AS `module_nom`, `e`.`nom` AS `etudiant_nom`, `e`.`prenom` AS `etudiant_prenom`, avg(`n`.`note`) AS `moyenne_module`, count(`n`.`id`) AS `nombre_notes` FROM ((`modules` `m` join `notes` `n` on(`m`.`id` = `n`.`module_id`)) join `etudiants` `e` on(`n`.`etudiant_id` = `e`.`id`)) GROUP BY `m`.`id`, `e`.`id` ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_statistiques`
--
DROP TABLE IF EXISTS `v_statistiques`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_statistiques`  AS SELECT count(distinct `e`.`id`) AS `total_etudiants`, count(distinct `ens`.`id`) AS `total_enseignants`, count(distinct `m`.`id`) AS `total_modules`, avg(`e`.`moyenne`) AS `moyenne_generale`, count(distinct case when `e`.`statut` = 'Diplômé' then `e`.`id` end) AS `etudiants_diplomes` FROM ((`etudiants` `e` join `enseignants` `ens`) join `modules` `m`) WHERE `e`.`actif` = 1 AND `ens`.`actif` = 1 AND `m`.`actif` = 1 ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD KEY `idx_etudiant_date` (`etudiant_id`,`date_absence`),
  ADD KEY `idx_justifiee` (`justifiee`),
  ADD KEY `idx_absences_etudiant_date` (`etudiant_id`,`date_absence`);

--
-- Index pour la table `assignations`
--
ALTER TABLE `assignations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignation` (`enseignant_id`,`module_id`,`annee_academique`,`semestre`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `idx_annee_semestre` (`annee_academique`,`semestre`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_niveau` (`niveau`),
  ADD KEY `idx_filiere` (`filiere`),
  ADD KEY `idx_actif` (`actif`);

--
-- Index pour la table `creneaux_horaire`
--
ALTER TABLE `creneaux_horaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classe_id` (`classe_id`),
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD KEY `fk_creneaux_matiere` (`matiere_id`);

--
-- Index pour la table `deliberations`
--
ALTER TABLE `deliberations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_deliberation` (`etudiant_id`,`annee_academique`,`semestre`),
  ADD KEY `idx_annee_semestre` (`annee_academique`,`semestre`),
  ADD KEY `idx_decision` (`decision`),
  ADD KEY `idx_deliberations_etudiant_annee` (`etudiant_id`,`annee_academique`);

--
-- Index pour la table `emplois_du_temps`
--
ALTER TABLE `emplois_du_temps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD KEY `idx_jour_heure` (`jour`,`heure_debut`),
  ADD KEY `idx_salle` (`salle`),
  ADD KEY `idx_annee_semestre` (`annee_academique`,`semestre`),
  ADD KEY `idx_emplois_jour_heure` (`jour`,`heure_debut`,`heure_fin`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_matricule` (`matricule`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_actif` (`actif`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_matricule` (`matricule`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_niveau` (`niveau`),
  ADD KEY `idx_filiere` (`filiere`),
  ADD KEY `idx_actif` (`actif`);

--
-- Index pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_priorite` (`priorite`);

--
-- Index pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscription` (`etudiant_id`,`module_id`,`annee_academique`,`semestre`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `idx_annee_semestre` (`annee_academique`,`semestre`);

--
-- Index pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_niveau` (`niveau`),
  ADD KEY `idx_semestre` (`semestre`),
  ADD KEY `idx_actif` (`actif`);

--
-- Index pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD KEY `idx_etudiant_module` (`etudiant_id`,`module_id`),
  ADD KEY `idx_annee_semestre` (`annee_academique`,`semestre`),
  ADD KEY `idx_type_evaluation` (`type_evaluation`),
  ADD KEY `idx_notes_etudiant_module` (`etudiant_id`,`module_id`,`annee_academique`),
  ADD KEY `fk_notes_matiere` (`matiere_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_actif` (`actif`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absences`
--
ALTER TABLE `absences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `assignations`
--
ALTER TABLE `assignations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `creneaux_horaire`
--
ALTER TABLE `creneaux_horaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `deliberations`
--
ALTER TABLE `deliberations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emplois_du_temps`
--
ALTER TABLE `emplois_du_temps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `evenements`
--
ALTER TABLE `evenements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absences_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absences_ibfk_3` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `assignations`
--
ALTER TABLE `assignations`
  ADD CONSTRAINT `assignations_ibfk_1` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignations_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `creneaux_horaire`
--
ALTER TABLE `creneaux_horaire`
  ADD CONSTRAINT `creneaux_horaire_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `creneaux_horaire_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `creneaux_horaire_ibfk_3` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_creneaux_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `deliberations`
--
ALTER TABLE `deliberations`
  ADD CONSTRAINT `deliberations_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `emplois_du_temps`
--
ALTER TABLE `emplois_du_temps`
  ADD CONSTRAINT `emplois_du_temps_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emplois_du_temps_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_3` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
