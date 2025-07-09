<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Mon Profil";
require_once dirname(__DIR__) . '/includes/header.php';

$message = '';
$error = '';

// Récupération des données utilisateur
$user = getUtilisateur($_SESSION['user_id']);

// Traitement de la modification du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Erreur de sécurité.';
    } else {
        switch ($_POST['action']) {
            case 'update_profile':
                $result = updateProfile($_POST);
                if ($result['success']) {
                    $message = 'Profil mis à jour avec succès.';
                    $user = getUtilisateur($_SESSION['user_id']); // Recharger les données
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'change_password':
                $result = changePassword($_POST);
                if ($result['success']) {
                    $message = 'Mot de passe modifié avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Fonctions
function getUtilisateur($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function updateProfile($data) {
    try {
        $pdo = getDBConnection();
        
        // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
        }
        
        $stmt = $pdo->prepare("
            UPDATE utilisateurs SET 
                nom = ?, prenom = ?, email = ?, telephone = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $_SESSION['user_id']
        ]);
        
        logActivity('modification_profil', 'Modification du profil utilisateur');
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function changePassword($data) {
    try {
        $pdo = getDBConnection();
        
        // Vérifier l'ancien mot de passe
        $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!password_verify($data['ancien_mot_de_passe'], $user['mot_de_passe'])) {
            return ['success' => false, 'message' => 'L\'ancien mot de passe est incorrect.'];
        }
        
        // Vérifier que les nouveaux mots de passe correspondent
        if ($data['nouveau_mot_de_passe'] !== $data['confirmation_mot_de_passe']) {
            return ['success' => false, 'message' => 'Les nouveaux mots de passe ne correspondent pas.'];
        }
        
        // Mettre à jour le mot de passe
        $nouveau_mot_de_passe_hash = password_hash($data['nouveau_mot_de_passe'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$nouveau_mot_de_passe_hash, $_SESSION['user_id']]);
        
        logActivity('changement_mot_de_passe', 'Changement de mot de passe');
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors du changement: ' . $e->getMessage()];
    }
}

$roles = [
    'admin' => 'Administrateur',
    'enseignant' => 'Enseignant',
    'secretaire' => 'Secrétaire'
];
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle me-2"></i>Mon Profil
                    </h1>
                    <p class="text-muted mb-0">Gérez vos informations personnelles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Informations du profil -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="profileForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                                           value="<?php echo htmlspecialchars($user['telephone']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <input type="text" class="form-control" id="role" 
                                   value="<?php echo $roles[$user['role']] ?? $user['role']; ?>" readonly>
                            <div class="form-text">Le rôle ne peut être modifié que par un administrateur</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Changement de mot de passe -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock me-2"></i>Changer le Mot de Passe
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="passwordForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="change_password">

                        <div class="mb-3">
                            <label for="ancien_mot_de_passe" class="form-label">Ancien mot de passe *</label>
                            <input type="password" class="form-control" id="ancien_mot_de_passe" name="ancien_mot_de_passe" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe *</label>
                                    <input type="password" class="form-control" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required>
                                    <div class="form-text">Minimum 8 caractères</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmation_mot_de_passe" class="form-label">Confirmation *</label>
                                    <input type="password" class="form-control" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations du compte -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations du Compte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <i class="fas fa-user-circle fa-6x text-primary"></i>
                        </div>
                        <h5><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></h5>
                        <p class="text-muted"><?php echo $roles[$user['role']] ?? $user['role']; ?></p>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>ID Utilisateur</span>
                            <span class="badge bg-secondary"><?php echo $user['id']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Email</span>
                            <span class="text-muted"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Date de création</span>
                            <span class="text-muted"><?php echo date('d/m/Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Statut</span>
                            <span class="badge bg-success">Actif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Connexion</h6>
                                <p class="timeline-text">Dernière connexion aujourd'hui</p>
                                <small class="text-muted"><?php echo date('H:i'); ?></small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Profil mis à jour</h6>
                                <p class="timeline-text">Informations personnelles modifiées</p>
                                <small class="text-muted">Récemment</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 0.8rem;
    color: #6c757d;
}
</style>

<script>
// Validation du formulaire de profil
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['nom', 'prenom', 'email'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
    }
});

// Validation du formulaire de mot de passe
document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['ancien_mot_de_passe', 'nouveau_mot_de_passe', 'confirmation_mot_de_passe'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    // Vérifier que les nouveaux mots de passe correspondent
    const nouveauMotDePasse = document.getElementById('nouveau_mot_de_passe').value;
    const confirmationMotDePasse = document.getElementById('confirmation_mot_de_passe').value;
    
    if (nouveauMotDePasse !== confirmationMotDePasse) {
        document.getElementById('confirmation_mot_de_passe').classList.add('is-invalid');
        isValid = false;
    }
    
    // Vérifier la longueur du mot de passe
    if (nouveauMotDePasse.length < 8) {
        document.getElementById('nouveau_mot_de_passe').classList.add('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Veuillez vérifier les informations saisies.');
    }
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 