-- Script de mise à jour des rôles utilisateurs
-- Changement de 'personnel' vers 'etudiant'

USE edugestion;

-- Mettre à jour les utilisateurs existants avec le rôle 'personnel'
UPDATE utilisateurs SET role = 'etudiant' WHERE role = 'personnel';

-- Modifier la structure de la table pour accepter 'etudiant' au lieu de 'personnel'
ALTER TABLE utilisateurs MODIFY COLUMN role ENUM('admin', 'enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant';

-- Vérifier les changements
SELECT id, nom, prenom, role FROM utilisateurs WHERE actif = 1; 