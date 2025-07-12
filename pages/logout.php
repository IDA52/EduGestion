<?php
/**
 * Page de déconnexion
 * Déconnecte l'utilisateur et redirige vers la page de connexion
 */

require_once dirname(__DIR__) . '/config.php';

// Vérification de l'authentification
if (!isAuthenticated()) {
    redirect('login');
}

// Récupération des informations utilisateur avant déconnexion
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';

// Journalisation de la déconnexion
if ($user_id) {
    logActivity('deconnexion', 'Déconnexion de l\'utilisateur: ' . $user_name);
}

// Destruction de toutes les variables de session
$_SESSION = array();

// Destruction du cookie de session si il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruction de la session
session_destroy();

// Redirection vers la page de connexion avec un message de succès
$_SESSION['flash_message'] = 'Vous avez été déconnecté avec succès.';
$_SESSION['flash_type'] = 'success';

// Redirection
redirect('home');
?> 