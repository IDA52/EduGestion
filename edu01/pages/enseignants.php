<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Gestion des Enseignants";
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
                $result = addEnseignant($_POST);
                if ($result['success']) {
                    $message = 'Enseignant ajouté avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateEnseignant($_POST);
                if ($result['success']) {
                    $message = 'Enseignant modifié avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteEnseignant($_POST['id']);
                if ($result['success']) {
                    $message = 'Enseignant supprimé avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$enseignants = getEnseignants();
$enseignant = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $enseignant = getEnseignant($_GET['id']);
    if (!$enseignant) {
        $error = 'Enseignant non trouvé.';
        $action = 'list';
    }
}

// Fonctions
function getEnseignants() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM enseignants WHERE actif = 1 ORDER BY nom, prenom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getEnseignant($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function addEnseignant($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO enseignants (matricule, nom, prenom, email, telephone, date_naissance, 
                                   adresse, specialite, grade, date_embauche, salaire)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['matricule'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['date_naissance'] ?: null,
            $data['adresse'],
            $data['specialite'],
            $data['grade'],
            $data['date_embauche'] ?: null,
            $data['salaire'] ?: null
        ]);
        
        logActivity('ajout_enseignant', 'Ajout de l\'enseignant ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateEnseignant($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE enseignants SET 
                matricule = ?, nom = ?, prenom = ?, email = ?, telephone = ?,
                date_naissance = ?, adresse = ?, specialite = ?, grade = ?,
                date_embauche = ?, salaire = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['matricule'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['date_naissance'] ?: null,
            $data['adresse'],
            $data['specialite'],
            $data['grade'],
            $data['date_embauche'] ?: null,
            $data['salaire'] ?: null,
            $data['id']
        ]);
        
        logActivity('modification_enseignant', 'Modification de l\'enseignant ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteEnseignant($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE enseignants SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_enseignant', 'Suppression de l\'enseignant ID: ' . $id);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
    }
}
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Gestion des Enseignants
                    </h1>
                    <p class="text-muted mb-0">Gérez les enseignants de l'établissement</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvel Enseignant
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
        <!-- Liste des enseignants -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Enseignants
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Spécialité</th>
                                <th>Grade</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enseignants as $ens): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($ens['matricule']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($ens['nom'] . ' ' . $ens['prenom']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($ens['email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($ens['email']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($ens['specialite']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($ens['grade']); ?></td>
                                <td><?php echo htmlspecialchars($ens['telephone']); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $ens['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $ens['id']; ?>, '<?php echo htmlspecialchars($ens['nom'] . ' ' . $ens['prenom']); ?>')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                    <?php echo $action === 'add' ? 'Nouvel Enseignant' : 'Modifier l\'Enseignant'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="enseignantForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($enseignant): ?>
                        <input type="hidden" name="id" value="<?php echo $enseignant['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="matricule" class="form-label">Matricule *</label>
                                <input type="text" class="form-control" id="matricule" name="matricule" 
                                       value="<?php echo htmlspecialchars($enseignant['matricule'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade" class="form-label">Grade</label>
                                <select class="form-select" id="grade" name="grade">
                                    <option value="">Sélectionner un grade</option>
                                    <option value="Professeur" <?php echo ($enseignant['grade'] ?? '') === 'Professeur' ? 'selected' : ''; ?>>Professeur</option>
                                    <option value="Maître de conférences" <?php echo ($enseignant['grade'] ?? '') === 'Maître de conférences' ? 'selected' : ''; ?>>Maître de conférences</option>
                                    <option value="Chargé de cours" <?php echo ($enseignant['grade'] ?? '') === 'Chargé de cours' ? 'selected' : ''; ?>>Chargé de cours</option>
                                    <option value="Assistant" <?php echo ($enseignant['grade'] ?? '') === 'Assistant' ? 'selected' : ''; ?>>Assistant</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($enseignant['nom'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($enseignant['prenom'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($enseignant['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($enseignant['telephone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                       value="<?php echo htmlspecialchars($enseignant['date_naissance'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_embauche" class="form-label">Date d'embauche</label>
                                <input type="date" class="form-control" id="date_embauche" name="date_embauche"
                                       value="<?php echo htmlspecialchars($enseignant['date_embauche'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="salaire" class="form-label">Salaire</label>
                                <input type="number" step="0.01" class="form-control" id="salaire" name="salaire"
                                       value="<?php echo htmlspecialchars($enseignant['salaire'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <input type="text" class="form-control" id="specialite" name="specialite" 
                               value="<?php echo htmlspecialchars($enseignant['specialite'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($enseignant['adresse'] ?? ''); ?></textarea>
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
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'enseignant "${name}" ?`)) {
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
document.getElementById('enseignantForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['matricule', 'nom', 'prenom', 'email'];
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
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 