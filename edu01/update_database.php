<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    echo "Mise à jour de la base de données...\n";
    
    // Mettre à jour les utilisateurs existants avec le rôle 'personnel'
    $stmt = $pdo->prepare("UPDATE utilisateurs SET role = 'etudiant' WHERE role = 'personnel'");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo "Utilisateurs mis à jour: $updated\n";
    
    // Modifier la structure de la table pour accepter 'etudiant' au lieu de 'personnel'
    $pdo->exec("ALTER TABLE utilisateurs MODIFY COLUMN role ENUM('admin', 'enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant'");
    echo "Structure de la table mise à jour.\n";
    
    // Vérifier les changements
    $stmt = $pdo->query("SELECT id, nom, prenom, role FROM utilisateurs WHERE actif = 1");
    $users = $stmt->fetchAll();
    
    echo "\nUtilisateurs actuels:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Nom: {$user['nom']} {$user['prenom']}, Rôle: {$user['role']}\n";
    }
    
    echo "\nMise à jour terminée avec succès!\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?> 