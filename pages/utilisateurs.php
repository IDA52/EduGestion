<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Gestion des Utilisateurs";
require_once dirname(__DIR__) . '/includes/header.php';

// Vérification des permissions
if (!hasPermission('admin')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('dashboard');
}

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Erreur de sécurité.';
    } else {
        switch ($_POST['action']) {
            case 'add':
                $result = addUtilisateur($_POST);
                if ($result['success']) {
                    $message = 'Utilisateur ajouté avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateUtilisateur($_POST);
                if ($result['success']) {
                    $message = 'Utilisateur modifié avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteUtilisateur($_POST['id']);
                if ($result['success']) {
                    $message = 'Utilisateur supprimé avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$utilisateurs = getUtilisateurs();
$utilisateur = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $utilisateur = getUtilisateur($_GET['id']);
    if (!$utilisateur) {
        $error = 'Utilisateur non trouvé.';
        $action = 'list';
    }
}

// Fonctions
function getUtilisateurs() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM utilisateurs WHERE actif = 1 ORDER BY nom, prenom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

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

function addUtilisateur($data) {
    try {
        $pdo = getDBConnection();
        
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, telephone)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $mot_de_passe_hash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $mot_de_passe_hash,
            $data['role'],
            $data['telephone']
        ]);
        
        logActivity('ajout_utilisateur', 'Ajout de l\'utilisateur ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateUtilisateur($data) {
    try {
        $pdo = getDBConnection();
        
        // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $data['id']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
        }
        
        if (!empty($data['mot_de_passe'])) {
            $stmt = $pdo->prepare("
                UPDATE utilisateurs SET 
                    nom = ?, prenom = ?, email = ?, mot_de_passe = ?, role = ?, telephone = ?
                WHERE id = ?
            ");
            
            $mot_de_passe_hash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
            
            $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $mot_de_passe_hash,
                $data['role'],
                $data['telephone'],
                $data['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE utilisateurs SET 
                    nom = ?, prenom = ?, email = ?, role = ?, telephone = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['role'],
                $data['telephone'],
                $data['id']
            ]);
        }
        
        logActivity('modification_utilisateur', 'Modification de l\'utilisateur ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteUtilisateur($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_utilisateur', 'Suppression de l\'utilisateur ID: ' . $id);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
    }
}

$roles = [
    'admin' => 'Administrateur',
    'enseignant' => 'Enseignant',
    'etudiant' => 'Étudiant'
];
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i>Gestion des Utilisateurs
                    </h1>
                    <p class="text-muted mb-0">Gérez les utilisateurs du système</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvel Utilisateur
                    </a>
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

    <?php if ($action === 'list'): ?>
        <!-- Liste des utilisateurs -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Utilisateurs
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Téléphone</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></div>
                                            <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php
                                    $role_class = $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'enseignant' ? 'bg-primary' : 'bg-info');
                                    ?>
                                    <span class="badge <?php echo $role_class; ?>"><?php echo $roles[$user['role']] ?? $user['role']; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($user['telephone']); ?></td>
                                <td>
                                    <span class="text-muted"><?php echo date('d/m/Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Formulaire d'ajout/modification -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                    <?php echo $action === 'add' ? 'Nouvel Utilisateur' : 'Modifier l\'Utilisateur'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="utilisateurForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($utilisateur): ?>
                        <input type="hidden" name="id" value="<?php echo $utilisateur['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($utilisateur['nom'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($utilisateur['telephone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Rôle *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <?php foreach ($roles as $role_value => $role_label): ?>
                                        <option value="<?php echo $role_value; ?>" 
                                                <?php echo ($utilisateur['role'] ?? '') === $role_value ? 'selected' : ''; ?>>
                                            <?php echo $role_label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">
                                    Mot de passe <?php echo $action === 'add' ? '*' : ''; ?>
                                </label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                                       <?php echo $action === 'add' ? 'required' : ''; ?>>
                                <?php if ($action === 'edit'): ?>
                                    <div class="form-text">Laissez vide pour ne pas modifier le mot de passe</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?php echo $action === 'add' ? 'Ajouter' : 'Modifier'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${name}" ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Validation du formulaire
document.getElementById('utilisateurForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['nom', 'prenom', 'email', 'role'];
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
    
    // Validation du mot de passe pour l'ajout
    const motDePasseInput = document.getElementById('mot_de_passe');
    const isAdd = <?php echo $action === 'add' ? 'true' : 'false'; ?>;
    
    if (isAdd && !motDePasseInput.value.trim()) {
        motDePasseInput.classList.add('is-invalid');
        isValid = false;
    } else {
        motDePasseInput.classList.remove('is-invalid');
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
    }
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 