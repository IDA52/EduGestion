-- =====================================================
-- Script de création de la base de données EduGestion
-- Version: 1.0
-- Auteur: EduGestion Team
-- =====================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `edugestion` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `edugestion`;

-- =====================================================
-- TABLE: utilisateurs (Administrateurs)
-- =====================================================
CREATE TABLE `utilisateurs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'enseignant', 'personnel') NOT NULL DEFAULT 'personnel',
    `telephone` VARCHAR(20) NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `derniere_connexion` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: enseignants
-- =====================================================
CREATE TABLE `enseignants` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `matricule` VARCHAR(20) NOT NULL UNIQUE,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `telephone` VARCHAR(20) NULL,
    `date_naissance` DATE NULL,
    `adresse` TEXT NULL,
    `specialite` VARCHAR(255) NULL,
    `grade` ENUM('Professeur', 'Maître de conférences', 'Chargé de cours', 'Assistant') NULL,
    `date_embauche` DATE NULL,
    `salaire` DECIMAL(10,2) NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `photo` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_matricule` (`matricule`),
    INDEX `idx_email` (`email`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: etudiants
-- =====================================================
CREATE TABLE `etudiants` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `matricule` VARCHAR(20) NOT NULL UNIQUE,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `telephone` VARCHAR(20) NULL,
    `date_naissance` DATE NULL,
    `lieu_naissance` VARCHAR(255) NULL,
    `adresse` TEXT NULL,
    `niveau` ENUM('L1', 'L2', 'L3', 'M1', 'M2', 'D') NOT NULL,
    `filiere` VARCHAR(255) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `moyenne` DECIMAL(4,2) NULL DEFAULT 0.00,
    `credits` INT(11) NULL DEFAULT 0,
    `statut` ENUM('Inscrit', 'En cours', 'Diplômé', 'Abandon') NOT NULL DEFAULT 'Inscrit',
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `photo` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_matricule` (`matricule`),
    INDEX `idx_email` (`email`),
    INDEX `idx_niveau` (`niveau`),
    INDEX `idx_filiere` (`filiere`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE etudiants
  ADD COLUMN date_inscription DATE NULL AFTER annee_academique,
  ADD COLUMN sexe ENUM('M','F') NULL AFTER prenom;

-- =====================================================
-- TABLE: modules
-- =====================================================
CREATE TABLE `modules` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `nom` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `credits` INT(11) NOT NULL DEFAULT 3,
    `coefficient` DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    `niveau` ENUM('L1', 'L2', 'L3', 'M1', 'M2', 'D') NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `heures_cours` INT(11) NOT NULL DEFAULT 0,
    `heures_td` INT(11) NOT NULL DEFAULT 0,
    `heures_tp` INT(11) NOT NULL DEFAULT 0,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_code` (`code`),
    INDEX `idx_niveau` (`niveau`),
    INDEX `idx_semestre` (`semestre`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: assignations (Enseignant-Module)
-- =====================================================
CREATE TABLE `assignations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enseignant_id` INT(11) NOT NULL,
    `module_id` INT(11) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `heures_attribuees` INT(11) NOT NULL DEFAULT 0,
    `date_debut` DATE NULL,
    `date_fin` DATE NULL,
    `statut` ENUM('En cours', 'Terminé', 'Annulé') NOT NULL DEFAULT 'En cours',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_assignation` (`enseignant_id`, `module_id`, `annee_academique`, `semestre`),
    FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
    INDEX `idx_annee_semestre` (`annee_academique`, `semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: inscriptions (Étudiant-Module)
-- =====================================================
CREATE TABLE `inscriptions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `etudiant_id` INT(11) NOT NULL,
    `module_id` INT(11) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `date_inscription` DATE NOT NULL,
    `statut` ENUM('Inscrit', 'En cours', 'Validé', 'Échoué', 'Abandon') NOT NULL DEFAULT 'Inscrit',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_inscription` (`etudiant_id`, `module_id`, `annee_academique`, `semestre`),
    FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
    INDEX `idx_annee_semestre` (`annee_academique`, `semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: notes
-- =====================================================
CREATE TABLE `notes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `etudiant_id` INT(11) NOT NULL,
    `module_id` INT(11) NOT NULL,
    `enseignant_id` INT(11) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `type_evaluation` ENUM('Contrôle continu', 'Examen', 'TP', 'Projet') NOT NULL,
    `note` DECIMAL(4,2) NOT NULL,
    `coefficient` DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    `date_evaluation` DATE NOT NULL,
    `commentaire` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants`(`id`) ON DELETE CASCADE,
    INDEX `idx_etudiant_module` (`etudiant_id`, `module_id`),
    INDEX `idx_annee_semestre` (`annee_academique`, `semestre`),
    INDEX `idx_type_evaluation` (`type_evaluation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: emplois_du_temps
-- =====================================================
CREATE TABLE `emplois_du_temps` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `module_id` INT(11) NOT NULL,
    `enseignant_id` INT(11) NOT NULL,
    `jour` ENUM('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi') NOT NULL,
    `heure_debut` TIME NOT NULL,
    `heure_fin` TIME NOT NULL,
    `salle` VARCHAR(50) NOT NULL,
    `type_cours` ENUM('Cours', 'TD', 'TP') NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants`(`id`) ON DELETE CASCADE,
    INDEX `idx_jour_heure` (`jour`, `heure_debut`),
    INDEX `idx_salle` (`salle`),
    INDEX `idx_annee_semestre` (`annee_academique`, `semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: absences
-- =====================================================
CREATE TABLE `absences` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `etudiant_id` INT(11) NOT NULL,
    `module_id` INT(11) NOT NULL,
    `enseignant_id` INT(11) NOT NULL,
    `date_absence` DATE NOT NULL,
    `heure_debut` TIME NOT NULL,
    `heure_fin` TIME NOT NULL,
    `justifiee` TINYINT(1) NOT NULL DEFAULT 0,
    `justificatif` TEXT NULL,
    `motif` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants`(`id`) ON DELETE CASCADE,
    INDEX `idx_etudiant_date` (`etudiant_id`, `date_absence`),
    INDEX `idx_justifiee` (`justifiee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: deliberations
-- =====================================================
CREATE TABLE `deliberations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `etudiant_id` INT(11) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `semestre` ENUM('S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10') NOT NULL,
    `moyenne_semestre` DECIMAL(4,2) NOT NULL,
    `credits_obtenus` INT(11) NOT NULL DEFAULT 0,
    `credits_totaux` INT(11) NOT NULL DEFAULT 0,
    `decision` ENUM('Admis', 'Admis avec conditions', 'Redoublement', 'Exclusion') NOT NULL,
    `mention` ENUM('Passable', 'Assez bien', 'Bien', 'Très bien', 'Excellent') NULL,
    `date_deliberation` DATE NOT NULL,
    `commentaire` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_deliberation` (`etudiant_id`, `annee_academique`, `semestre`),
    FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants`(`id`) ON DELETE CASCADE,
    INDEX `idx_annee_semestre` (`annee_academique`, `semestre`),
    INDEX `idx_decision` (`decision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: evenements
-- =====================================================
CREATE TABLE `evenements` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `titre` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `date` DATE NOT NULL,
    `heure_debut` TIME NULL,
    `heure_fin` TIME NULL,
    `lieu` VARCHAR(255) NULL,
    `type` ENUM('Examen', 'Réunion', 'Conférence', 'Autre') NOT NULL DEFAULT 'Autre',
    `priorite` ENUM('Basse', 'Normale', 'Haute', 'Urgente') NOT NULL DEFAULT 'Normale',
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_date` (`date`),
    INDEX `idx_type` (`type`),
    INDEX `idx_priorite` (`priorite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: logs (Journal d'activité)
-- =====================================================
CREATE TABLE `logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NULL,
    `action` VARCHAR(255) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `utilisateurs`(`id`) ON DELETE SET NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: login_attempts (Tentatives de connexion)
-- =====================================================
CREATE TABLE `login_attempts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(45) NOT NULL,
    `email` VARCHAR(255) NULL,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ip_address` (`ip_address`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTION DES DONNÉES DE TEST
-- =====================================================

-- Utilisateurs de test (mots de passe en clair pour les tests)
INSERT INTO `utilisateurs` (`nom`, `prenom`, `email`, `mot_de_passe`, `role`, `telephone`) VALUES
('Admin', 'Principal', 'admin@edugestion.com', 'admin123', 'admin', '+1234567890'),
('Dupont', 'Marie', 'prof@edugestion.com', 'prof123', 'enseignant', '+1234567891'),
('Martin', 'Jean', 'personnel@edugestion.com', 'personnel123', 'personnel', '+1234567892');

-- Enseignants de test
INSERT INTO `enseignants` (`matricule`, `nom`, `prenom`, `email`, `telephone`, `specialite`, `grade`, `date_embauche`) VALUES
('ENS001', 'Dupont', 'Marie', 'marie.dupont@edugestion.com', '+1234567891', 'Informatique', 'Professeur', '2020-09-01'),
('ENS002', 'Bernard', 'Pierre', 'pierre.bernard@edugestion.com', '+1234567893', 'Mathématiques', 'Maître de conférences', '2021-03-15'),
('ENS003', 'Leroy', 'Sophie', 'sophie.leroy@edugestion.com', '+1234567894', 'Physique', 'Chargé de cours', '2022-01-10');

-- Étudiants de test
INSERT INTO `etudiants` (`matricule`, `nom`, `prenom`, `email`, `telephone`, `niveau`, `filiere`, `annee_academique`, `moyenne`, `credits`) VALUES
('ETU001', 'Martin', 'Thomas', 'thomas.martin@student.com', '+1234567895', 'L2', 'Informatique', '2023-2024', 14.50, 60),
('ETU002', 'Dubois', 'Emma', 'emma.dubois@student.com', '+1234567896', 'L3', 'Mathématiques', '2023-2024', 16.20, 90),
('ETU003', 'Moreau', 'Lucas', 'lucas.moreau@student.com', '+1234567897', 'M1', 'Physique', '2023-2024', 15.80, 120);

-- Modules de test
INSERT INTO `modules` (`code`, `nom`, `description`, `credits`, `coefficient`, `niveau`, `semestre`, `heures_cours`, `heures_td`, `heures_tp`) VALUES
('INFO101', 'Programmation Java', 'Introduction à la programmation orientée objet avec Java', 6, 2.00, 'L2', 'S3', 20, 20, 20),
('MATH201', 'Algèbre linéaire', 'Étude des espaces vectoriels et applications linéaires', 5, 1.50, 'L2', 'S3', 15, 15, 0),
('PHYS301', 'Mécanique quantique', 'Principes fondamentaux de la mécanique quantique', 4, 1.00, 'M1', 'S7', 25, 10, 5);

-- Assignations de test
INSERT INTO `assignations` (`enseignant_id`, `module_id`, `annee_academique`, `semestre`, `heures_attribuees`) VALUES
(1, 1, '2023-2024', 'S3', 60),
(2, 2, '2023-2024', 'S3', 30),
(3, 3, '2023-2024', 'S7', 40);

-- Inscriptions de test
INSERT INTO `inscriptions` (`etudiant_id`, `module_id`, `annee_academique`, `semestre`, `date_inscription`, `statut`) VALUES
(1, 1, '2023-2024', 'S3', '2023-09-01', 'En cours'),
(2, 2, '2023-2024', 'S3', '2023-09-01', 'En cours'),
(3, 3, '2023-2024', 'S7', '2023-09-01', 'En cours');

-- Notes de test
INSERT INTO `notes` (`etudiant_id`, `module_id`, `enseignant_id`, `annee_academique`, `semestre`, `type_evaluation`, `note`, `coefficient`, `date_evaluation`) VALUES
(1, 1, 1, '2023-2024', 'S3', 'Contrôle continu', 15.50, 1.00, '2023-10-15'),
(1, 1, 1, '2023-2024', 'S3', 'Examen', 14.00, 2.00, '2023-12-20'),
(2, 2, 2, '2023-2024', 'S3', 'Contrôle continu', 16.00, 1.00, '2023-10-20'),
(3, 3, 3, '2023-2024', 'S7', 'Projet', 17.50, 1.50, '2023-11-10');

-- Emplois du temps de test
INSERT INTO `emplois_du_temps` (`module_id`, `enseignant_id`, `jour`, `heure_debut`, `heure_fin`, `salle`, `type_cours`, `annee_academique`, `semestre`) VALUES
(1, 1, 'Lundi', '08:00:00', '10:00:00', 'A101', 'Cours', '2023-2024', 'S3'),
(1, 1, 'Mercredi', '14:00:00', '16:00:00', 'TP101', 'TP', '2023-2024', 'S3'),
(2, 2, 'Mardi', '10:00:00', '12:00:00', 'A102', 'Cours', '2023-2024', 'S3'),
(3, 3, 'Jeudi', '16:00:00', '18:00:00', 'A103', 'Cours', '2023-2024', 'S7');

-- Événements de test
INSERT INTO `evenements` (`titre`, `description`, `date`, `heure_debut`, `heure_fin`, `lieu`, `type`, `priorite`) VALUES
('Examen Java', 'Examen final du module Programmation Java', '2023-12-20', '14:00:00', '16:00:00', 'Salle A101', 'Examen', 'Haute'),
('Réunion pédagogique', 'Réunion des enseignants du département informatique', '2023-12-15', '10:00:00', '12:00:00', 'Salle de réunion', 'Réunion', 'Normale'),
('Conférence IA', 'Conférence sur l\'intelligence artificielle', '2023-12-25', '18:00:00', '20:00:00', 'Amphithéâtre', 'Conférence', 'Basse');

-- =====================================================
-- TRIGGERS POUR CALCULS AUTOMATIQUES
-- =====================================================

-- Trigger pour calculer la moyenne d'un étudiant
DELIMITER //
CREATE TRIGGER `update_student_average` 
AFTER INSERT ON `notes`
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;

-- Trigger pour calculer les crédits obtenus
DELIMITER //
CREATE TRIGGER `update_student_credits` 
AFTER INSERT ON `notes`
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;

-- =====================================================
-- VUES UTILES
-- =====================================================

-- Vue pour les moyennes par module
CREATE VIEW `v_moyennes_modules` AS
SELECT 
    m.code,
    m.nom as module_nom,
    e.nom as etudiant_nom,
    e.prenom as etudiant_prenom,
    AVG(n.note) as moyenne_module,
    COUNT(n.id) as nombre_notes
FROM modules m
JOIN notes n ON m.id = n.module_id
JOIN etudiants e ON n.etudiant_id = e.id
GROUP BY m.id, e.id;

-- Vue pour les emplois du temps
CREATE VIEW `v_emplois_temps` AS
SELECT 
    edt.jour,
    edt.heure_debut,
    edt.heure_fin,
    edt.salle,
    edt.type_cours,
    m.nom as module_nom,
    CONCAT(ens.nom, ' ', ens.prenom) as enseignant_nom,
    edt.annee_academique,
    edt.semestre
FROM emplois_du_temps edt
JOIN modules m ON edt.module_id = m.id
JOIN enseignants ens ON edt.enseignant_id = ens.id
WHERE edt.actif = 1
ORDER BY 
    FIELD(edt.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'),
    edt.heure_debut;

-- Vue pour les statistiques générales
CREATE VIEW `v_statistiques` AS
SELECT 
    COUNT(DISTINCT e.id) as total_etudiants,
    COUNT(DISTINCT ens.id) as total_enseignants,
    COUNT(DISTINCT m.id) as total_modules,
    AVG(e.moyenne) as moyenne_generale,
    COUNT(DISTINCT CASE WHEN e.statut = 'Diplômé' THEN e.id END) as etudiants_diplomes
FROM etudiants e
CROSS JOIN enseignants ens
CROSS JOIN modules m
WHERE e.actif = 1 AND ens.actif = 1 AND m.actif = 1;

-- =====================================================
-- INDEX OPTIMISÉS
-- =====================================================

-- Index pour les recherches fréquentes
CREATE INDEX `idx_notes_etudiant_module` ON `notes` (`etudiant_id`, `module_id`, `annee_academique`);
CREATE INDEX `idx_emplois_jour_heure` ON `emplois_du_temps` (`jour`, `heure_debut`, `heure_fin`);
CREATE INDEX `idx_absences_etudiant_date` ON `absences` (`etudiant_id`, `date_absence`);
CREATE INDEX `idx_deliberations_etudiant_annee` ON `deliberations` (`etudiant_id`, `annee_academique`);

-- =====================================================
-- FIN DU SCRIPT
-- =====================================================

-- Afficher un message de confirmation
SELECT 'Base de données EduGestion créée avec succès !' as message; 


--------------------------------

CREATE TABLE `matieres` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `nom` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `coefficient` DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    `couleur` VARCHAR(20) DEFAULT '#22c55e',
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO matieres (code, nom, description) VALUES ('MAT101', 'Mathématiques', 'Cours de mathématiques de base');

------------------------------------------------
------------------------------------------------
CREATE TABLE `classes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `niveau` ENUM('L1', 'L2', 'L3', 'M1', 'M2', 'D') NOT NULL,
    `filiere` VARCHAR(100) NOT NULL,
    `annee_academique` VARCHAR(9) NOT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_niveau` (`niveau`),
    INDEX `idx_filiere` (`filiere`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE classes ADD COLUMN niveau_id INT NULL AFTER nom;


------------------------------------------------
------------------------------------------------

CREATE TABLE `creneaux_horaire` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `jour` ENUM('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi') NOT NULL,
    `heure_debut` TIME NOT NULL,
    `heure_fin` TIME NOT NULL,
    `salle` VARCHAR(100) NOT NULL,
    `type_cours` ENUM('Cours', 'TD', 'TP') NOT NULL,
    `matiere_id` INT(11) NOT NULL,
    `enseignant_id` INT(11) NOT NULL,
    `classe_id` INT(11) NOT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`matiere_id`) REFERENCES `matieres`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    INDEX `idx_jour` (`jour`),
    INDEX `idx_heure` (`heure_debut`, `heure_fin`),
    INDEX `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE creneaux_horaire
  ADD CONSTRAINT fk_creneaux_matiere
  FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE SET NULL;

ALTER TABLE creneaux_horaire MODIFY COLUMN matiere_id INT(11) NULL;
ALTER TABLE matieres MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;
------------------------------------------------
------------------------------------------------
CREATE TABLE `niveaux` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(20) NOT NULL,         -- ex : L1, L2, L3, M1, M2, D
    `ordre` INT NOT NULL DEFAULT 1,     -- pour trier les niveaux
    `actif` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO niveaux (nom, ordre) VALUES
('L1', 1),
('L2', 2),
('L3', 3),
('M1', 4),
('M2', 5),
('D', 6);

ALTER TABLE classes ADD CONSTRAINT fk_classes_niveau FOREIGN KEY (niveau_id) REFERENCES niveaux(id) ON DELETE SET NULL;

ALTER TABLE classes ADD COLUMN capacite INT NULL AFTER niveau_id;

ALTER TABLE classes ADD COLUMN description TEXT NULL AFTER capacite;

ALTER TABLE classes ADD COLUMN annee_scolaire VARCHAR(9) NULL AFTER description;