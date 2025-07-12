<?php
require_once 'config.php';

echo "<h1>Debug - Table emplois_du_temps</h1>";

try {
    $pdo = getDBConnection();
    
    // Vérifier si la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'emplois_du_temps'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La table 'emplois_du_temps' n'existe pas !</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ La table 'emplois_du_temps' existe.</p>";
    
    // Afficher la structure de la table
    echo "<h2>Structure de la table :</h2>";
    $stmt = $pdo->query("DESCRIBE emplois_du_temps");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Compter les enregistrements
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM emplois_du_temps");
    $count = $stmt->fetch()['total'];
    echo "<h2>Nombre d'enregistrements : $count</h2>";
    
    // Afficher quelques enregistrements
    if ($count > 0) {
        echo "<h2>Données (limitées à 10) :</h2>";
        $stmt = $pdo->query("SELECT * FROM emplois_du_temps LIMIT 10");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Module ID</th><th>Enseignant ID</th><th>Jour</th><th>Heure début</th><th>Heure fin</th><th>Salle</th><th>Type cours</th><th>Année</th><th>Semestre</th><th>Actif</th></tr>";
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['module_id'] . "</td>";
            echo "<td>" . $row['enseignant_id'] . "</td>";
            echo "<td>" . $row['jour'] . "</td>";
            echo "<td>" . $row['heure_debut'] . "</td>";
            echo "<td>" . $row['heure_fin'] . "</td>";
            echo "<td>" . $row['salle'] . "</td>";
            echo "<td>" . $row['type_cours'] . "</td>";
            echo "<td>" . $row['annee_academique'] . "</td>";
            echo "<td>" . $row['semestre'] . "</td>";
            echo "<td>" . $row['actif'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Vérifier les tables liées
    echo "<h2>Vérification des tables liées :</h2>";
    
    // Modules
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM modules");
    $modules_count = $stmt->fetch()['total'];
    echo "<p>Modules : $modules_count enregistrements</p>";
    
    // Enseignants
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enseignants");
    $enseignants_count = $stmt->fetch()['total'];
    echo "<p>Enseignants : $enseignants_count enregistrements</p>";
    
    // Test de la requête de la page emploi_temps
    echo "<h2>Test de la requête de la page emploi_temps :</h2>";
    $sql = "
        SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
        FROM emplois_du_temps e
        LEFT JOIN modules m ON e.module_id = m.id
        LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
        WHERE e.actif = 1
        ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
    ";
    
    try {
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<p style='color: green;'>✅ Requête exécutée avec succès. Résultats : " . count($results) . "</p>";
        
        if (count($results) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Jour</th><th>Heure</th><th>Module</th><th>Enseignant</th><th>Salle</th><th>Type</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . $row['jour'] . "</td>";
                echo "<td>" . substr($row['heure_debut'], 0, 5) . " - " . substr($row['heure_fin'], 0, 5) . "</td>";
                echo "<td>" . ($row['module_nom'] ?? 'N/A') . "</td>";
                echo "<td>" . (($row['enseignant_nom'] ?? '') . ' ' . ($row['enseignant_prenom'] ?? '')) . "</td>";
                echo "<td>" . $row['salle'] . "</td>";
                echo "<td>" . $row['type_cours'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur dans la requête : " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
}
?> 