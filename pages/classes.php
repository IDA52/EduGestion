<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Gestion des Classes";
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
                $result = addClasse($_POST);
                if ($result['success']) {
                    $message = 'Classe ajoutée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateClasse($_POST);
                if ($result['success']) {
                    $message = 'Classe modifiée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteClasse($_POST['id']);
                if ($result['success']) {
                    $message = 'Classe supprimée avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$classes = getClasses();
$classe = null;
$niveaux = getNiveaux();

if ($action === 'edit' && isset($_GET['id'])) {
    $classe = getClasse($_GET['id']);
    if (!$classe) {
        $error = 'Classe non trouvée.';
        $action = 'list';
    }
}

// Fonctions
function getClasses() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT c.*, n.nom as niveau_nom, 
                   COUNT(e.id) as nb_etudiants
            FROM classes c 
            LEFT JOIN niveaux n ON c.niveau_id = n.id 
            LEFT JOIN etudiants e ON c.id = e.classe_id AND e.actif = 1
            WHERE c.actif = 1 
            GROUP BY c.id
            ORDER BY n.ordre, c.nom
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getClasse($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function getNiveaux() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM niveaux WHERE actif = 1 ORDER BY ordre");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function addClasse($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO classes (nom, niveau_id, capacite, description, annee_scolaire)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['nom'],
            $data['niveau_id'] ?: null,
            $data['capacite'] ?: null,
            $data['description'],
            $data['annee_scolaire'] ?: date('Y')
        ]);
        
        logActivity('ajout_classe', 'Ajout de la classe ' . $data['nom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateClasse($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE classes SET 
                nom = ?, niveau_id = ?, capacite = ?, description = ?, annee_scolaire = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['nom'],
            $data['niveau_id'] ?: null,
            $data['capacite'] ?: null,
            $data['description'],
            $data['annee_scolaire'] ?: date('Y'),
            $data['id']
        ]);
        
        logActivity('modification_classe', 'Modification de la classe ' . $data['nom']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteClasse($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE classes SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_classe', 'Suppression de la classe ID: ' . $id);
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
                        <i class="fas fa-school me-2"></i>Gestion des Classes
                    </h1>
                    <p class="text-muted mb-0">Gérez les classes de l'établissement</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Classe
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
        <!-- Liste des classes -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Classes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th>Niveau</th>
                                <th>Étudiants</th>
                                <th>Capacité</th>
                                <th>Année Scolaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $cl): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($cl['nom']); ?></div>
                                            <?php if ($cl['description']): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($cl['description']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($cl['niveau_nom']): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($cl['niveau_nom']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Non défini</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo $cl['nb_etudiants']; ?> étudiants</span>
                                </td>
                                <td>
                                    <?php if ($cl['capacite']): ?>
                                        <span class="badge bg-warning"><?php echo $cl['capacite']; ?> places</span>
                                    <?php else: ?>
                                        <span class="text-muted">Illimitée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $cl['annee_scolaire']; ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $cl['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $cl['id']; ?>, '<?php echo htmlspecialchars($cl['nom']); ?>')"
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
                    <?php echo $action === 'add' ? 'Nouvelle Classe' : 'Modifier la Classe'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="classeForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($classe): ?>
                        <input type="hidden" name="id" value="<?php echo $classe['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de la classe *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($classe['nom'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="niveau_id" class="form-label">Niveau</label>
                                <select class="form-select" id="niveau_id" name="niveau_id">
                                    <option value="">Sélectionner un niveau</option>
                                    <?php foreach ($niveaux as $niveau): ?>
                                        <option value="<?php echo $niveau['id']; ?>" 
                                                <?php echo ($classe['niveau_id'] ?? '') == $niveau['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($niveau['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacite" class="form-label">Capacité</label>
                                <input type="number" class="form-control" id="capacite" name="capacite" 
                                       value="<?php echo $classe['capacite'] ?? ''; ?>" min="1">
                                <div class="form-text">Laissez vide pour une capacité illimitée</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="annee_scolaire" class="form-label">Année Scolaire</label>
                                <input type="number" class="form-control" id="annee_scolaire" name="annee_scolaire" 
                                       value="<?php echo $classe['annee_scolaire'] ?? date('Y'); ?>" min="2000" max="2100">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($classe['description'] ?? ''); ?></textarea>
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
    if (confirm(`Êtes-vous sûr de vouloir supprimer la classe "${name}" ?`)) {
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
document.getElementById('classeForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['nom'];
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