<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Étudiants";
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
                $result = addEtudiant($_POST);
                if ($result['success']) {
                    $message = 'Étudiant ajouté avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateEtudiant($_POST);
                if ($result['success']) {
                    $message = 'Étudiant modifié avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteEtudiant($_POST['id']);
                if ($result['success']) {
                    $message = 'Étudiant supprimé avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$etudiants = getEtudiants();
$etudiant = null;
$classes = getClasses();

if ($action === 'edit' && isset($_GET['id'])) {
    $etudiant = getEtudiant($_GET['id']);
    if (!$etudiant) {
        $error = 'Étudiant non trouvé.';
        $action = 'list';
    }
}

// Fonctions
function getEtudiants() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT e.*, c.nom as classe_nom 
            FROM etudiants e 
            LEFT JOIN classes c ON e.classe_id = c.id 
            WHERE e.actif = 1 
            ORDER BY e.nom, e.prenom
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getEtudiant($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function getClasses() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM classes WHERE actif = 1 ORDER BY nom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function addEtudiant($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO etudiants (matricule, nom, prenom, email, telephone, date_naissance, 
                                   adresse, classe_id, date_inscription, sexe)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['matricule'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['date_naissance'] ?: null,
            $data['adresse'],
            $data['classe_id'] ?: null,
            $data['date_inscription'] ?: date('Y-m-d'),
            $data['sexe']
        ]);
        
        logActivity('ajout_etudiant', 'Ajout de l\'étudiant ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateEtudiant($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE etudiants SET 
                matricule = ?, nom = ?, prenom = ?, email = ?, telephone = ?,
                date_naissance = ?, adresse = ?, classe_id = ?, sexe = ?
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
            $data['classe_id'] ?: null,
            $data['sexe'],
            $data['id']
        ]);
        
        logActivity('modification_etudiant', 'Modification de l\'étudiant ' . $data['nom'] . ' ' . $data['prenom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteEtudiant($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE etudiants SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_etudiant', 'Suppression de l\'étudiant ID: ' . $id);
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
                        <i class="fas fa-user-graduate me-2"></i>Gestion des Étudiants
                    </h1>
                    <p class="text-muted mb-0">Gérez les étudiants de l'établissement</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvel Étudiant
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
        <!-- Liste des étudiants -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Étudiants
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
                                <th>Classe</th>
                                <th>Sexe</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etudiants as $etud): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($etud['matricule']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($etud['nom'] . ' ' . $etud['prenom']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($etud['email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($etud['email']); ?></td>
                                <td>
                                    <?php if ($etud['classe_nom']): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($etud['classe_nom']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Non assigné</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($etud['sexe'] === 'M'): ?>
                                        <span class="badge bg-primary">Masculin</span>
                                    <?php else: ?>
                                        <span class="badge bg-pink">Féminin</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($etud['telephone']); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $etud['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $etud['id']; ?>, '<?php echo htmlspecialchars($etud['nom'] . ' ' . $etud['prenom']); ?>')"
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
                    <?php echo $action === 'add' ? 'Nouvel Étudiant' : 'Modifier l\'Étudiant'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="etudiantForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($etudiant): ?>
                        <input type="hidden" name="id" value="<?php echo $etudiant['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="matricule" class="form-label">Matricule *</label>
                                <input type="text" class="form-control" id="matricule" name="matricule" 
                                       value="<?php echo htmlspecialchars($etudiant['matricule'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="classe_id" class="form-label">Classe</label>
                                <select class="form-select" id="classe_id" name="classe_id">
                                    <option value="">Sélectionner une classe</option>
                                    <?php foreach ($classes as $classe): ?>
                                        <option value="<?php echo $classe['id']; ?>" 
                                                <?php echo ($etudiant['classe_id'] ?? '') == $classe['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($classe['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($etudiant['nom'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($etudiant['prenom'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($etudiant['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($etudiant['telephone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" 
                                       value="<?php echo $etudiant['date_naissance'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sexe" class="form-label">Sexe</label>
                                <select class="form-select" id="sexe" name="sexe">
                                    <option value="">Sélectionner</option>
                                    <option value="M" <?php echo ($etudiant['sexe'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculin</option>
                                    <option value="F" <?php echo ($etudiant['sexe'] ?? '') === 'F' ? 'selected' : ''; ?>>Féminin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date_inscription" class="form-label">Date d'inscription</label>
                        <input type="date" class="form-control" id="date_inscription" name="date_inscription"
                               value="<?php echo htmlspecialchars($etudiant['date_inscription'] ?? date('Y-m-d')); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($etudiant['adresse'] ?? ''); ?></textarea>
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
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'étudiant "${name}" ?`)) {
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
document.getElementById('etudiantForm')?.addEventListener('submit', function(e) {
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