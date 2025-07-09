<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Gestion des Matières";
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
                $result = addMatiere($_POST);
                if ($result['success']) {
                    $message = 'Matière ajoutée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateMatiere($_POST);
                if ($result['success']) {
                    $message = 'Matière modifiée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteMatiere($_POST['id']);
                if ($result['success']) {
                    $message = 'Matière supprimée avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$matieres = getMatieres();
$matiere = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $matiere = getMatiere($_GET['id']);
    if (!$matiere) {
        $error = 'Matière non trouvée.';
        $action = 'list';
    }
}

// Fonctions
function getMatieres() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM matieres WHERE actif = 1 ORDER BY nom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getMatiere($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function addMatiere($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO matieres (code, nom, description, coefficient, couleur)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['code'],
            $data['nom'],
            $data['description'],
            $data['coefficient'] ?: 1,
            $data['couleur'] ?: '#007bff'
        ]);
        
        logActivity('ajout_matiere', 'Ajout de la matière ' . $data['nom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateMatiere($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE matieres SET 
                code = ?, nom = ?, description = ?, coefficient = ?, couleur = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['code'],
            $data['nom'],
            $data['description'],
            $data['coefficient'] ?: 1,
            $data['couleur'] ?: '#007bff',
            $data['id']
        ]);
        
        logActivity('modification_matiere', 'Modification de la matière ' . $data['nom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteMatiere($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE matieres SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_matiere', 'Suppression de la matière ID: ' . $id);
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
                        <i class="fas fa-book me-2"></i>Gestion des Matières
                    </h1>
                    <p class="text-muted mb-0">Gérez les matières enseignées</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Matière
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
        <!-- Liste des matières -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Matières
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Matière</th>
                                <th>Coefficient</th>
                                <th>Couleur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matieres as $mat): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($mat['code']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <i class="fas fa-book fa-2x" style="color: <?php echo $mat['couleur']; ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($mat['nom']); ?></div>
                                            <?php if ($mat['description']): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($mat['description']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $mat['coefficient']; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: <?php echo $mat['couleur']; ?>; border-radius: 4px;"></div>
                                        <span class="text-muted"><?php echo $mat['couleur']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $mat['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $mat['id']; ?>, '<?php echo htmlspecialchars($mat['nom']); ?>')"
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
                    <?php echo $action === 'add' ? 'Nouvelle Matière' : 'Modifier la Matière'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="matiereForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($matiere): ?>
                        <input type="hidden" name="id" value="<?php echo $matiere['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code *</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?php echo htmlspecialchars($matiere['code'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($matiere['nom'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="coefficient" class="form-label">Coefficient</label>
                                <input type="number" class="form-control" id="coefficient" name="coefficient" 
                                       value="<?php echo $matiere['coefficient'] ?? 1; ?>" min="0.1" step="0.1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="couleur" class="form-label">Couleur</label>
                                <input type="color" class="form-control form-control-color" id="couleur" name="couleur" 
                                       value="<?php echo $matiere['couleur'] ?? '#007bff'; ?>" title="Choisir une couleur">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($matiere['description'] ?? ''); ?></textarea>
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
    if (confirm(`Êtes-vous sûr de vouloir supprimer la matière "${name}" ?`)) {
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
document.getElementById('matiereForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['code', 'nom'];
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