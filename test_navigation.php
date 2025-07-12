<?php
require_once 'config.php';

echo "<h1>Test de Navigation - EduGestion</h1>";

// Test de connexion à la base de données
try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
    exit;
}

// Test des routes
$routes = [
    'dashboard' => 'pages/dashboard.php',
    'emploi_temps' => 'pages/emploi_temps.php',
    'profil' => 'pages/profil.php',
    'logout' => 'pages/logout.php'
];

echo "<h2>Test des fichiers de pages :</h2>";
foreach ($routes as $route => $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $route : $file existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $route : $file n'existe pas</p>";
    }
}

// Test des includes
$includes = [
    'config.php',
    'includes/header.php',
    'includes/footer.php'
];

echo "<h2>Test des fichiers includes :</h2>";
foreach ($includes as $include) {
    if (file_exists($include)) {
        echo "<p style='color: green;'>✅ $include existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $include n'existe pas</p>";
    }
}

// Test de la table emplois_du_temps
echo "<h2>Test de la table emplois_du_temps :</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'emplois_du_temps'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La table emplois_du_temps existe</p>";
        
        // Compter les enregistrements
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM emplois_du_temps");
        $count = $stmt->fetch()['total'];
        echo "<p>📊 Nombre d'enregistrements : $count</p>";
        
        // Tester la requête de la page emploi_temps
        $sql = "
            SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
            FROM emplois_du_temps e
            LEFT JOIN modules m ON e.module_id = m.id
            LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
            WHERE e.actif = 1
            ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
        ";
        
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<p style='color: green;'>✅ Requête emploi_temps fonctionne : " . count($results) . " résultats</p>";
        
    } else {
        echo "<p style='color: red;'>❌ La table emplois_du_temps n'existe pas</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur lors du test de la table : " . $e->getMessage() . "</p>";
}

// Test des utilisateurs
echo "<h2>Test des utilisateurs :</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs");
    $count = $stmt->fetch()['total'];
    echo "<p>👥 Nombre d'utilisateurs : $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, nom, prenom, email, role FROM utilisateurs LIMIT 3");
        $users = $stmt->fetchAll();
        echo "<p>📋 Exemples d'utilisateurs :</p>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>" . $user['nom'] . " " . $user['prenom'] . " (" . $user['role'] . ") - " . $user['email'] . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur lors du test des utilisateurs : " . $e->getMessage() . "</p>";
}

echo "<h2>Liens de test :</h2>";
echo "<p><a href='index.php' target='_blank'>🏠 Page d'accueil</a></p>";
echo "<p><a href='pages/login.php' target='_blank'>🔐 Page de connexion</a></p>";
echo "<p><a href='install.php' target='_blank'>⚙️ Installation</a></p>";

echo "<h2>Configuration :</h2>";
echo "<p><strong>APP_URL :</strong> " . APP_URL . "</p>";
echo "<p><strong>APP_NAME :</strong> " . APP_NAME . "</p>";
echo "<p><strong>Base path :</strong> /edu01/</p>";
?> 