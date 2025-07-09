<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
// Vérification des permissions
if (!hasPermission('admin') && !hasPermission('enseignant')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('dashboard');
}
$pageTitle = "Gestion des Notes";
require_once dirname(__DIR__) . '/includes/header.php';

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
                $result = addNote($_POST);
                if ($result['success']) {
                    $message = 'Note ajoutée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'edit':
                $result = updateNote($_POST);
                if ($result['success']) {
                    $message = 'Note modifiée avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteNote($_POST['id']);
                if ($result['success']) {
                    $message = 'Note supprimée avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
$isAdmin = hasPermission('admin');
$isEnseignant = hasPermission('enseignant') && !$isAdmin;

if ($isAdmin) {
    $notes = getNotes();
    $modules = getModules();
    $etudiants = getEtudiants();
    $enseignants = getEnseignant();
} else if ($isEnseignant) {
    // Récupérer l'id de l'enseignant à partir de l'email utilisateur
    $enseignant_id = null;
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM enseignants WHERE email = ? AND actif = 1");
        $stmt->execute([$_SESSION['user_email']]);
        $row = $stmt->fetch();
        if ($row) {
            $enseignant_id = $row['id'];
        }
    } catch (PDOException $e) {
        $enseignant_id = null;
    }
    $notes = getNotes($enseignant_id);
    $modules = getModulesByEnseignant($enseignant_id);
    $etudiants = getEtudiantsByModules($modules);
    $enseignants = [];
} else {
    $notes = [];
    $modules = [];
    $etudiants = [];
    $enseignants = [];
}
$note = null;
$classes = getClasses();

if ($action === 'edit' && isset($_GET['id'])) {
    $note = getNote($_GET['id']);
    if (!$note) {
        $error = 'Note non trouvée.';
        $action = 'list';
    }
}

// Fonctions
function getNotes($enseignant_id = null) {
    try {
        $pdo = getDBConnection();
        $sql = "
            SELECT n.*, 
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.matricule as etudiant_matricule,
                   c.nom as classe_nom,
                   m.nom as module_nom, m.code as module_code,
                   ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
            FROM notes n
            LEFT JOIN etudiants e ON n.etudiant_id = e.id
            LEFT JOIN classes c ON e.classe_id = c.id
            LEFT JOIN modules m ON n.module_id = m.id
            LEFT JOIN enseignants ens ON n.enseignant_id = ens.id
        ";
        $params = [];
        if ($enseignant_id) {
            $sql .= " WHERE n.enseignant_id = ? ";
            $params[] = $enseignant_id;
        }
        $sql .= " ORDER BY n.annee_academique DESC, n.semestre, e.nom, e.prenom, m.nom ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getNote($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function getEtudiants() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM etudiants WHERE actif = 1 ORDER BY nom, prenom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getModules() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM modules WHERE actif = 1 ORDER BY nom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
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

function getEnseignant() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM enseignants WHERE actif = 1 ORDER BY nom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getModulesByEnseignant($enseignant_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT m.* FROM modules m INNER JOIN emplois_du_temps e ON m.id = e.module_id WHERE e.enseignant_id = ? AND m.actif = 1 GROUP BY m.id ORDER BY m.nom");
        $stmt->execute([$enseignant_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getEtudiantsByModules($modules) {
    if (empty($modules)) return [];
    $module_ids = array_map(function($m) { return $m['id']; }, $modules);
    $placeholders = implode(',', array_fill(0, count($module_ids), '?'));
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT DISTINCT e.* FROM etudiants e INNER JOIN inscriptions i ON e.id = i.etudiant_id WHERE i.module_id IN ($placeholders) AND e.actif = 1 ORDER BY e.nom, e.prenom");
        $stmt->execute($module_ids);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function addNote($data) {
    try {
        $pdo = getDBConnection();
        global $isAdmin, $isEnseignant, $enseignant_id;
        if ($isEnseignant) {
            $data['enseignant_id'] = $enseignant_id;
        }
        // Vérification d'unicité
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE etudiant_id = ? AND module_id = ? AND type_evaluation = ? AND annee_academique = ? AND semestre = ?");
        $stmt->execute([
            $data['etudiant_id'],
            $data['module_id'],
            $data['type_evaluation'],
            $data['annee_academique'],
            $data['semestre']
        ]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => "Une note existe déjà pour cet étudiant, ce module, ce type d'évaluation, ce semestre et cette année académique."];
        }
        $stmt = $pdo->prepare("
            INSERT INTO notes (etudiant_id, module_id, enseignant_id, annee_academique, semestre, type_evaluation, note, coefficient, date_evaluation, commentaire)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['etudiant_id'],
            $data['module_id'],
            $data['enseignant_id'],
            $data['annee_academique'],
            $data['semestre'],
            $data['type_evaluation'],
            $data['note'],
            $data['coefficient'] ?: 1,
            $data['date_evaluation'] ?: date('Y-m-d'),
            $data['commentaire']
        ]);
        logActivity('ajout_note', 'Ajout d\'une note pour l\'étudiant ID: ' . $data['etudiant_id']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateNote($data) {
    try {
        $pdo = getDBConnection();
        global $isAdmin, $isEnseignant, $enseignant_id;
        if ($isEnseignant) {
            $data['enseignant_id'] = $enseignant_id;
        }
        $stmt = $pdo->prepare("
            UPDATE notes SET 
                etudiant_id = ?, module_id = ?, enseignant_id = ?, annee_academique = ?, semestre = ?, type_evaluation = ?, note = ?, coefficient = ?, date_evaluation = ?, commentaire = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['etudiant_id'],
            $data['module_id'],
            $data['enseignant_id'],
            $data['annee_academique'],
            $data['semestre'],
            $data['type_evaluation'],
            $data['note'],
            $data['coefficient'] ?: 1,
            $data['date_evaluation'] ?: date('Y-m-d'),
            $data['commentaire'],
            $data['id']
        ]);
        logActivity('modification_note', 'Modification d\'une note ID: ' . $data['id']);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteNote($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE notes SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity('suppression_note', 'Suppression d\'une note ID: ' . $id);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
    }
}

$types_evaluation = [
    'Contrôle' => 'Contrôle',
    'Devoir' => 'Devoir',
    'Examen' => 'Examen',
    'TP' => 'Travaux Pratiques',
    'Oral' => 'Oral',
    'Autre' => 'Autre'
];
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-star me-2"></i>Gestion des Notes
                    </h1>
                    <p class="text-muted mb-0">Gérez les notes des étudiants</p>
                </div>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Note
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
        <!-- Liste des notes -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Notes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Classe</th>
                                <th>Module</th>
                                <th>Enseignant</th>
                                <th>Année</th>
                                <th>Semestre</th>
                                <th>Type</th>
                                <th>Note</th>
                                <th>Coef.</th>
                                <th>Date</th>
                                <th>Commentaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $n): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars(($n['etudiant_nom'] ?? '') . ' ' . ($n['etudiant_prenom'] ?? '')); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($n['etudiant_matricule'] ?? ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($n['classe_nom'] ?? ''); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($n['module_nom'] ?? ''); ?></span></td>
                                <td><?php echo htmlspecialchars(($n['enseignant_nom'] ?? '') . ' ' . ($n['enseignant_prenom'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($n['annee_academique'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($n['semestre'] ?? ''); ?></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($n['type_evaluation'] ?? ''); ?></span></td>
                                <td><span class="badge bg-success fs-6"><?php echo $n['note']; ?>/20</span></td>
                                <td><span class="badge bg-secondary"><?php echo $n['coefficient']; ?></span></td>
                                <td><span class="text-muted"><?php echo isset($n['date_evaluation']) ? date('d/m/Y', strtotime($n['date_evaluation'])) : ''; ?></span></td>
                                <td><?php echo htmlspecialchars($n['commentaire'] ?? ''); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit&id=<?php echo $n['id']; ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $n['id']; ?>, '<?php echo htmlspecialchars(($n['etudiant_nom'] ?? '') . ' ' . ($n['etudiant_prenom'] ?? '')); ?>')"
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
                    <?php echo $action === 'add' ? 'Nouvelle Note' : 'Modifier la Note'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="noteForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($note): ?>
                        <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="etudiant_id" class="form-label">Étudiant *</label>
                                <select class="form-select" id="etudiant_id" name="etudiant_id" required>
                                    <option value="">Sélectionner un étudiant</option>
                                    <?php foreach ($etudiants as $etudiant): ?>
                                        <option value="<?php echo $etudiant['id']; ?>" <?php echo ($note['etudiant_id'] ?? '') == $etudiant['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom'] . ' (' . $etudiant['matricule'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module_id" class="form-label">Module *</label>
                                <select class="form-select" id="module_id" name="module_id" required>
                                    <option value="">Sélectionner un module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?php echo $module['id']; ?>" <?php echo ($note['module_id'] ?? '') == $module['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($module['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="enseignant_id" class="form-label">Enseignant *</label>
                                <select class="form-select" id="enseignant_id" name="enseignant_id" required>
                                    <option value="">Sélectionner un enseignant</option>
                                    <?php foreach ($enseignants as $enseignant): ?>
                                        <option value="<?php echo $enseignant['id']; ?>" <?php echo ($note['enseignant_id'] ?? '') == $enseignant['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php elseif ($isEnseignant): ?>
                            <input type="hidden" name="enseignant_id" value="<?php echo $enseignant_id; ?>">
                        <?php endif; ?>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="annee_academique" class="form-label">Année académique *</label>
                                <input type="text" class="form-control" id="annee_academique" name="annee_academique" value="<?php echo $note['annee_academique'] ?? date('Y') . '-' . (date('Y')+1); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="semestre" class="form-label">Semestre *</label>
                                <select class="form-select" id="semestre" name="semestre" required>
                                    <option value="">Sélectionner</option>
                                    <?php foreach (["S1","S2","S3","S4","S5","S6","S7","S8","S9","S10"] as $sem): ?>
                                        <option value="<?php echo $sem; ?>" <?php echo ($note['semestre'] ?? '') == $sem ? 'selected' : ''; ?>><?php echo $sem; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="type_evaluation" class="form-label">Type d'évaluation *</label>
                                <select class="form-select" id="type_evaluation" name="type_evaluation" required>
                                    <option value="">Sélectionner</option>
                                    <?php foreach (["Contrôle continu","Examen","TP","Projet"] as $type): ?>
                                        <option value="<?php echo $type; ?>" <?php echo ($note['type_evaluation'] ?? '') == $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="note" class="form-label">Note *</label>
                                <input type="number" class="form-control" id="note" name="note" value="<?php echo $note['note'] ?? ''; ?>" min="0" max="20" step="0.01" required>
                                <div class="form-text">Note sur 20</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="coefficient" class="form-label">Coefficient</label>
                                <input type="number" class="form-control" id="coefficient" name="coefficient" value="<?php echo $note['coefficient'] ?? 1; ?>" min="0.1" step="0.1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_evaluation" class="form-label">Date d'évaluation</label>
                                <input type="date" class="form-control" id="date_evaluation" name="date_evaluation" value="<?php echo $note['date_evaluation'] ?? date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="2"><?php echo htmlspecialchars($note['commentaire'] ?? ''); ?></textarea>
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
    if (confirm(`Êtes-vous sûr de vouloir supprimer la note de "${name}" ?`)) {
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
document.getElementById('noteForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['etudiant_id', 'module_id', 'note'];
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
    
    // Validation de la note
    const noteInput = document.getElementById('note');
    if (noteInput.value) {
        const noteValue = parseFloat(noteInput.value);
        if (noteValue < 0 || noteValue > 20) {
            noteInput.classList.add('is-invalid');
            isValid = false;
        } else {
            noteInput.classList.remove('is-invalid');
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires et vérifier que la note est entre 0 et 20.');
    }
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 