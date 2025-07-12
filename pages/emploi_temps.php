<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Emploi du temps";
require_once dirname(__DIR__) . '/includes/header.php';

// Vérification des permissions
if (!hasPermission('admin') && !hasPermission('enseignant')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('dashboard');
}

// Seul l'admin peut ajouter/éditer/supprimer
$isAdmin = hasPermission('admin');
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Empêcher l'accès aux actions add/edit pour les non-admins
if (!$isAdmin && ($action === 'add' || $action === 'edit')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('emploi_temps');
}

// Traitement des actions (seulement pour admin)
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Erreur de sécurité.';
    } else {
        switch ($_POST['action']) {
            case 'add':
                $result = addEmploi($_POST);
                if ($result['success']) {
                    $message = 'Créneau ajouté avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
            case 'edit':
                $result = updateEmploi($_POST);
                if ($result['success']) {
                    $message = 'Créneau modifié avec succès.';
                    $action = 'list';
                } else {
                    $error = $result['message'];
                }
                break;
            case 'delete':
                $result = deleteEmploi($_POST['id']);
                if ($result['success']) {
                    $message = 'Créneau supprimé avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des données
if ($isAdmin) {
    $emplois = getEmplois();
} else if (hasPermission('enseignant')) {
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
    $emplois = getEmplois($enseignant_id);
} else {
    $emplois = [];
}
$emploi = null;
$modules = getModules();
$enseignants = getEnseignants();
$annees = getAnneesAcademiques();
$semestres = ['S1','S2','S3','S4','S5','S6','S7','S8','S9','S10'];
$types_cours = ['Cours','TD','TP'];
$jours = [
    'Lundi' => 'Lundi',
    'Mardi' => 'Mardi', 
    'Mercredi' => 'Mercredi',
    'Jeudi' => 'Jeudi',
    'Vendredi' => 'Vendredi',
    'Samedi' => 'Samedi'
];

// DEBUG: Afficher le nombre de créneaux trouvés
if ($action === 'list') {
    echo "<!-- DEBUG: Nombre de créneaux trouvés: " . count($emplois) . " -->";
    if (count($emplois) == 0) {
        echo "<!-- DEBUG: Aucun créneau trouvé. Vérifiez la base de données. -->";
    }
}

if ($action === 'edit' && isset($_GET['id'])) {
    $emploi = getEmploi($_GET['id']);
    if (!$emploi) {
        $error = 'Créneau non trouvé.';
        $action = 'list';
    }
}

// Fonctions
function getEmplois($enseignant_id = null) {
    try {
        $pdo = getDBConnection();
        $sql = "
            SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
            FROM emplois_du_temps e
            LEFT JOIN modules m ON e.module_id = m.id
            LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
            WHERE e.actif = 1
        ";
        $params = [];
        if ($enseignant_id) {
            $sql .= " AND e.enseignant_id = ? ";
            $params[] = $enseignant_id;
        }
        $sql .= " ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DEBUG: Erreur SQL dans getEmplois(): " . $e->getMessage());
        return [];
    }
}

function getEmploi($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM emplois_du_temps WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
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

function getEnseignants() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM enseignants WHERE actif = 1 ORDER BY nom, prenom");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getAnneesAcademiques() {
    $annee = (int)date('Y');
    $annees = [];
    for ($i = 0; $i < 5; $i++) {
        $annees[] = ($annee-$i-1) . '-' . ($annee-$i);
    }
    return $annees;
}

function addEmploi($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO emplois_du_temps (module_id, enseignant_id, jour, heure_debut, heure_fin, salle, type_cours, annee_academique, semestre)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['module_id'],
            $data['enseignant_id'],
            $data['jour'],
            $data['heure_debut'],
            $data['heure_fin'],
            $data['salle'],
            $data['type_cours'],
            $data['annee_academique'],
            $data['semestre']
        ]);
        logActivity('ajout_emploi', 'Ajout d\'un créneau emploi du temps');
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
    }
}

function updateEmploi($data) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE emplois_du_temps SET 
                module_id = ?, enseignant_id = ?, jour = ?, heure_debut = ?, heure_fin = ?, salle = ?, type_cours = ?, annee_academique = ?, semestre = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['module_id'],
            $data['enseignant_id'],
            $data['jour'],
            $data['heure_debut'],
            $data['heure_fin'],
            $data['salle'],
            $data['type_cours'],
            $data['annee_academique'],
            $data['semestre'],
            $data['id']
        ]);
        logActivity('modification_emploi', 'Modification d\'un créneau emploi du temps');
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
}

function deleteEmploi($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE emplois_du_temps SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);
        logActivity('suppression_emploi', 'Suppression d\'un créneau emploi du temps ID: ' . $id);
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
                        <i class="fas fa-calendar-alt me-2"></i>Emploi du Temps
                    </h1>
                    <p class="text-muted mb-0">Gérez les emplois du temps des classes</p>
                </div>
                <?php if ($isAdmin): ?>
                <div>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouveau Créneau
                    </a>
                </div>
                <?php endif; ?>
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
        <!-- Vue emploi du temps -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar me-2"></i>Emploi du Temps
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Heure</th>
                                <th>Module</th>
                                <th>Enseignant</th>
                                <th>Salle</th>
                                <th>Type</th>
                                <th>Année</th>
                                <th>Semestre</th>
                                <?php if ($isAdmin): ?><th>Actions</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($emplois as $e): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($e['jour']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($e['heure_debut'],0,5)) . ' - ' . htmlspecialchars(substr($e['heure_fin'],0,5)); ?></td>
                                    <td><span class="badge" style="background:<?php echo $e['module_couleur'] ?? '#007bff'; ?>;color:#fff;"><?php echo htmlspecialchars($e['module_nom']); ?></span></td>
                                    <td><?php echo htmlspecialchars($e['enseignant_nom'] . ' ' . $e['enseignant_prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($e['salle']); ?></td>
                                    <td><?php echo htmlspecialchars($e['type_cours']); ?></td>
                                    <td><?php echo htmlspecialchars($e['annee_academique']); ?></td>
                                    <td><?php echo htmlspecialchars($e['semestre']); ?></td>
                                    <?php if ($isAdmin): ?>
                                    <td>
                                        <a href="?action=edit&id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Supprimer ce créneau ?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif (($action === 'add' || $action === 'edit') && $isAdmin): ?>
        <!-- Formulaire d'ajout/modification (admin uniquement) -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                    <?php echo $action === 'add' ? 'Nouveau Créneau' : 'Modifier le Créneau'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="emploiForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($emploi): ?>
                        <input type="hidden" name="id" value="<?php echo $emploi['id']; ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module_id" class="form-label">Module *</label>
                                <select class="form-select" id="module_id" name="module_id" required>
                                    <option value="">Choisir un module</option>
                                    <?php foreach ($modules as $m): ?>
                                        <option value="<?php echo $m['id']; ?>" <?php echo ($emploi['module_id'] ?? '') == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="enseignant_id" class="form-label">Enseignant *</label>
                                <select class="form-select" id="enseignant_id" name="enseignant_id" required>
                                    <option value="">Choisir un enseignant</option>
                                    <?php foreach ($enseignants as $ens): ?>
                                        <option value="<?php echo $ens['id']; ?>" <?php echo ($emploi['enseignant_id'] ?? '') == $ens['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ens['nom'] . ' ' . $ens['prenom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jour" class="form-label">Jour *</label>
                                <select class="form-select" id="jour" name="jour" required>
                                    <option value="">Choisir un jour</option>
                                    <?php foreach ($jours as $jval => $jlabel): ?>
                                        <option value="<?php echo $jval; ?>" <?php echo ($emploi['jour'] ?? '') === $jval ? 'selected' : ''; ?>><?php echo $jlabel; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="heure_debut" class="form-label">Heure de début *</label>
                                <input type="time" class="form-control" id="heure_debut" name="heure_debut" value="<?php echo $emploi['heure_debut'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="heure_fin" class="form-label">Heure de fin *</label>
                                <input type="time" class="form-control" id="heure_fin" name="heure_fin" value="<?php echo $emploi['heure_fin'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="salle" class="form-label">Salle *</label>
                                <input type="text" class="form-control" id="salle" name="salle" value="<?php echo htmlspecialchars($emploi['salle'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type_cours" class="form-label">Type de cours *</label>
                                <select class="form-select" id="type_cours" name="type_cours" required>
                                    <option value="">Choisir un type</option>
                                    <?php foreach ($types_cours as $type): ?>
                                        <option value="<?php echo $type; ?>" <?php echo ($emploi['type_cours'] ?? '') === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="annee_academique" class="form-label">Année académique *</label>
                                <select class="form-select" id="annee_academique" name="annee_academique" required>
                                    <option value="">Choisir une année</option>
                                    <?php foreach ($annees as $an): ?>
                                        <option value="<?php echo $an; ?>" <?php echo ($emploi['annee_academique'] ?? '') === $an ? 'selected' : ''; ?>><?php echo $an; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semestre" class="form-label">Semestre *</label>
                                <select class="form-select" id="semestre" name="semestre" required>
                                    <option value="">Choisir un semestre</option>
                                    <?php foreach ($semestres as $sem): ?>
                                        <option value="<?php echo $sem; ?>" <?php echo ($emploi['semestre'] ?? '') === $sem ? 'selected' : ''; ?>><?php echo $sem; ?></option>
                                    <?php endforeach; ?>
                                </select>
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
document.getElementById('emploiForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['module_id', 'enseignant_id', 'jour', 'heure_debut', 'heure_fin', 'salle', 'type_cours', 'annee_academique', 'semestre'];
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