<?php
/**
 * Configuration principale de l'application EduGestion
 * Version: 1.0
 * Auteur: EduGestion Team
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'edugestion');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration de l'application
define('APP_NAME', 'EduGestion');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/edu01');
define('APP_PATH', __DIR__);

// Configuration de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// Configuration des chemins
define('ASSETS_PATH', APP_PATH . '/assets');
define('INCLUDES_PATH', APP_PATH . '/includes');
define('PAGES_PATH', APP_PATH . '/pages');

// Configuration des uploads
define('UPLOAD_PATH', APP_PATH . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configuration des emails
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Configuration des langues
define('DEFAULT_LANG', 'fr');
define('AVAILABLE_LANGS', ['fr', 'en']);

// Configuration des timezones
date_default_timezone_set('Europe/Paris');

// Configuration des erreurs (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction de nettoyage des entrées
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Fonction de génération de token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction de vérification du token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fonction de redirection
function redirect($url) {
    // Si l'URL ne commence pas par http, on ajoute APP_URL
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = APP_URL . '/' . ltrim($url, '/');
    }
    header("Location: " . $url);
    exit();
}

// Fonction de vérification d'authentification
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Fonction de vérification des permissions
function hasPermission($requiredRole) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = $_SESSION['user_role'];
    $roleHierarchy = [
        'admin' => 3,
        'enseignant' => 2,
        'etudiant' => 1
    ];
    
    return isset($roleHierarchy[$userRole]) && 
           isset($roleHierarchy[$requiredRole]) && 
           $roleHierarchy[$userRole] >= $roleHierarchy[$requiredRole];
}

// Fonction de log
function logActivity($action, $details = '') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
}
?> 