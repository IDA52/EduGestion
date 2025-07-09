<?php
/**
 * Script d'installation d'EduGestion
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'edugestion';

echo "<h1>Installation d'EduGestion</h1>";
echo "<p>Configuration de la base de données...</p>";

try {
    // Connexion à MySQL sans spécifier de base de données
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connexion à MySQL réussie</p>";
    
    // Création de la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Base de données '$db_name' créée</p>";
    
    // Sélection de la base de données
    $pdo->exec("USE `$db_name`");
    
    // Lecture et exécution du script SQL
    $sql_file = __DIR__ . '/database/edugestion.sql';
    
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        
        // Supprimer les commentaires et les lignes vides
        $sql_content = preg_replace('/--.*$/m', '', $sql_content);
        $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);
        
        // Exécuter les requêtes
        $statements = explode(';', $sql_content);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignorer les erreurs de tables déjà existantes
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<p style='color: orange;'>⚠ " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p>✓ Tables créées avec succès</p>";
        echo "<p>✓ Données de test insérées</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Fichier SQL non trouvé: $sql_file</p>";
    }
    
    // Test de connexion avec la configuration de l'application
    require_once 'config.php';
    $test_pdo = getDBConnection();
    $stmt = $test_pdo->query("SELECT COUNT(*) FROM utilisateurs");
    $user_count = $stmt->fetchColumn();
    
    echo "<p>✓ Test de connexion réussi</p>";
    echo "<p>✓ $user_count utilisateur(s) trouvé(s) dans la base</p>";
    
    echo "<h2>Installation terminée avec succès !</h2>";
    echo "<p><strong>Comptes de test créés :</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@edugestion.com / admin123</li>";
    echo "<li><strong>Enseignant:</strong> prof@edugestion.com / prof123</li>";
    echo "<li><strong>Personnel:</strong> personnel@edugestion.com / personnel123</li>";
    echo "</ul>";
    
    echo "<p><a href='index.php' class='btn btn-primary'>Accéder à l'application</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Erreur de connexion à la base de données: " . $e->getMessage() . "</p>";
    echo "<p>Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>MySQL est démarré</li>";
    echo "<li>Les paramètres de connexion sont corrects</li>";
    echo "<li>L'utilisateur a les permissions nécessaires</li>";
    echo "</ul>";
}

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "h1 { color: #0d6efd; }";
echo "h2 { color: #198754; }";
echo ".btn { display: inline-block; padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }";
echo ".btn:hover { background: #0b5ed7; }";
echo "</style>";
?> 