<?php
/**
 * Point d'entrée principal de l'application EduGestion
 * Gestion du routage et de l'authentification
 */

require_once 'config.php';

// Gestion du routage
$request = $_SERVER['REQUEST_URI'];
$basePath = '/edu01/';

// Nettoyage de l'URL
$request = str_replace($basePath, '', $request);
$request = strtok($request, '?');
$request = trim($request, '/');

// Si la requête est vide, c'est la page d'accueil
if (empty($request)) {
    $request = '';
}

// Vérification de l'authentification
if (!isAuthenticated()) {
    // Pages publiques accessibles sans authentification
    $publicPages = ['', 'home', 'about', 'contact'];
    
    // Si l'utilisateur n'est pas connecté et accède à une page publique, l'autoriser
    if (in_array($request, $publicPages)) {
        if ($request === '' || $request === 'home') {
            include 'home.php';
            exit;
        }
        // Pour about et contact, continuer vers le routage normal
    } else {
        // Redirection vers la page de connexion pour les autres pages
        if ($request !== 'login') {
            redirect('login');
        }
    }
}

// Routes autorisées
$allowedRoutes = [
    '' => 'home.php',
    'dashboard' => 'pages/dashboard.php',
    'enseignants' => 'pages/enseignants.php',
    'etudiants' => 'pages/etudiants.php',
    'classes' => 'pages/classes.php',
    'matieres' => 'pages/matieres.php',
    'emploi_temps' => 'pages/emploi_temps.php',
    'notes' => 'pages/notes.php',
    'utilisateurs' => 'pages/utilisateurs.php',
    'profil' => 'pages/profil.php',
    'parametres' => 'pages/parametres.php',
    'rapports' => 'pages/rapports.php',
    'logout' => 'pages/logout.php',
    'login' => 'pages/login.php',
    'home' => 'home.php',
    'about' => 'about.php',
    'contact' => 'contact.php'
];

// Vérification de la route
if (isset($allowedRoutes[$request])) {
    $page = $allowedRoutes[$request];
    
    // Vérification des permissions pour certaines pages
    $adminPages = ['enseignants', 'etudiants', 'classes', 'matieres', 'utilisateurs', 'parametres', 'rapports'];
    $teacherPages = ['notes', 'emploi_temps'];
    
    if (in_array($request, $adminPages) && !hasPermission('admin')) {
        redirect('dashboard');
    }
    
    if (in_array($request, $teacherPages) && !hasPermission('enseignant')) {
        redirect('dashboard');
    }
    
    // Inclusion de la page
    if (file_exists($page)) {
        include $page;
    } else {
        include 'pages/404.php';
    }
} else {
    // Page 404 si route non trouvée
    include 'pages/404.php';
}
?> 