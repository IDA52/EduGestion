<?php
$pageTitle = "Connexion";
require_once dirname(__DIR__) . '/config.php';

// Redirection si déjà connecté
if (isAuthenticated()) {
    redirect('dashboard');
}

$error = '';
$success = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Vérification du token CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        // Vérification des tentatives de connexion
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = checkLoginAttempts($ip);
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $error = 'Trop de tentatives de connexion. Veuillez attendre 15 minutes.';
        } else {
            // Tentative de connexion
            $user = authenticateUser($email, $password);
            
            if ($user) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'] . ' ' . $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Gestion du "Se souvenir de moi"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setRememberToken($user['id'], $token);
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 jours
                }
                
                // Réinitialiser les tentatives
                resetLoginAttempts($ip);
                
                // Log de l'activité
                logActivity('connexion', 'Connexion réussie');
                
                // Redirection vers le tableau de bord
                redirect('dashboard');
            } else {
                // Échec de connexion
                incrementLoginAttempts($ip);
                $error = 'Email ou mot de passe incorrect.';
            }
        }
    }
}

// Fonction pour vérifier les tentatives de connexion
function checkLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$ip, LOCKOUT_TIME]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

// Fonction pour incrémenter les tentatives
function incrementLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, created_at) VALUES (?, NOW())");
        $stmt->execute([$ip]);
    } catch (PDOException $e) {
        // Ignorer les erreurs
    }
}

// Fonction pour réinitialiser les tentatives
function resetLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
    } catch (PDOException $e) {
        // Ignorer les erreurs
    }
}

// Fonction d'authentification
function authenticateUser($email, $password) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Pour les comptes de test, accepter les mots de passe en clair
            if ($user['email'] === $email && $user['password'] === $password) {
                return $user;
            }
            // if ($user['email'] === 'prof@edugestion.com' && $password === 'prof123') {
            //     return $user;
            // }
            // if ($user['email'] === 'personnel@edugestion.com' && $password === 'personnel123') {
            //     return $user;
            // }
            
            // Pour les autres comptes, utiliser password_verify
            if (password_verify($password, hash: $user['mot_de_passe'])) {
                return $user;
            }
        }
    } catch (PDOException $e) {
        // En cas d'erreur, retourner false
    }
    
    return false;
}

// Fonction pour définir le token "Se souvenir de moi"
function setRememberToken($user_id, $token) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE utilisateurs SET remember_token = ? WHERE id = ?");
        $stmt->execute([$token, $user_id]);
    } catch (PDOException $e) {
        // Ignorer les erreurs
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - EduGestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Particules animées */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 600px;
            padding: 2rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 2;
        }

        .input-with-icon {
            padding-left: 48px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }

        .form-check-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .test-accounts {
            margin-top: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .test-accounts h6 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .test-btn {
            display: block;
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 0.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: var(--dark-color);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .test-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .back-link a:hover {
            opacity: 1;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particules animées -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Container principal -->
    <div class="login-container">
        <div class="login-card">
            <!-- En-tête -->
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title">Connexion</h1>
                <p class="login-subtitle">Accédez à votre espace EduGestion</p>
            </div>

            <!-- Messages d'erreur/succès -->
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" action="" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" class="form-control input-with-icon" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control input-with-icon" id="password" name="password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="spinner" id="spinner"></span>
                    <span id="btnText">Se connecter</span>
                </button>
            </form>

            <!-- Comptes de test -->
            <div class="test-accounts">
                <h6><i class="fas fa-info-circle me-2"></i>Comptes de test</h6>
                <button class="test-btn" onclick="fillCredentials('admin@edugestion.com', 'admin123')">
                    <i class="fas fa-user-shield me-2"></i>Administrateur
                </button>
                <button class="test-btn" onclick="fillCredentials('prof@edugestion.com', 'prof123')">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Enseignant
                </button>
                <button class="test-btn" onclick="fillCredentials('etudiant@edugestion.com', 'etudiant123')">
                    <i class="fas fa-user me-2"></i>Étudiant
                </button>
            </div>
        </div>

        <!-- Lien de retour -->
        <div class="back-link">
            <a href="home">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à l'accueil
            </a>
        </div>
    </div>

    <script>
        // Toggle du mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Remplir les identifiants de test
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        // Validation et soumission du formulaire
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            
            // Validation basique
            if (!email || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs.');
                return;
            }
            
            // Validation email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide.');
                return;
            }
            
            // Afficher le spinner
            loginBtn.disabled = true;
            btnText.textContent = 'Connexion...';
            spinner.style.display = 'inline-block';
        });

        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const loginCard = document.querySelector('.login-card');
            loginCard.style.opacity = '0';
            loginCard.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                loginCard.style.transition = 'all 0.6s ease';
                loginCard.style.opacity = '1';
                loginCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html> 