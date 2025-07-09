<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Paramètres";
require_once dirname(__DIR__) . '/includes/header.php';

// Vérification des permissions
if (!hasPermission('admin')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('dashboard');
}

$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Erreur de sécurité.';
    } else {
        switch ($_POST['action']) {
            case 'update_settings':
                $result = updateSettings($_POST);
                if ($result['success']) {
                    $message = 'Paramètres mis à jour avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'backup_database':
                $result = backupDatabase();
                if ($result['success']) {
                    $message = 'Sauvegarde créée avec succès.';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Récupération des paramètres actuels
$settings = getSettings();

// Fonctions
function getSettings() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM parametres WHERE actif = 1");
        $params = $stmt->fetchAll();
        
        $settings = [];
        foreach ($params as $param) {
            $settings[$param['cle']] = $param['valeur'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

function updateSettings($data) {
    try {
        $pdo = getDBConnection();
        
        $settings_to_update = [
            'nom_etablissement' => $data['nom_etablissement'] ?? '',
            'adresse_etablissement' => $data['adresse_etablissement'] ?? '',
            'telephone_etablissement' => $data['telephone_etablissement'] ?? '',
            'email_etablissement' => $data['email_etablissement'] ?? '',
            'annee_scolaire' => $data['annee_scolaire'] ?? date('Y'),
            'semestre_actuel' => $data['semestre_actuel'] ?? '1',
            'note_minimale' => $data['note_minimale'] ?? '10',
            'note_maximale' => $data['note_maximale'] ?? '20',
            'delai_inscription' => $data['delai_inscription'] ?? '30',
            'activer_notifications' => $data['activer_notifications'] ?? '0',
            'activer_maintenance' => $data['activer_maintenance'] ?? '0'
        ];
        
        foreach ($settings_to_update as $cle => $valeur) {
            $stmt = $pdo->prepare("
                INSERT INTO parametres (cle, valeur, actif) 
                VALUES (?, ?, 1) 
                ON DUPLICATE KEY UPDATE valeur = ?
            ");
            $stmt->execute([$cle, $valeur, $valeur]);
        }
        
        logActivity('modification_parametres', 'Modification des paramètres système');
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
    }
}

function backupDatabase() {
    try {
        $backup_dir = dirname(__DIR__) . '/backups/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backup_dir . $filename;
        
        // Commande de sauvegarde MySQL
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s',
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            $filepath
        );
        
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            logActivity('sauvegarde_base', 'Sauvegarde de la base de données créée: ' . $filename);
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la sauvegarde'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()];
    }
}

function getSystemInfo() {
    $info = [
        'php_version' => PHP_VERSION,
        'mysql_version' => 'MySQL 8.0+',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'disk_free_space' => formatBytes(disk_free_space('/')),
        'disk_total_space' => formatBytes(disk_total_space('/')),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time') . 's',
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ];
    
    return $info;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-cogs me-2"></i>Paramètres
                    </h1>
                    <p class="text-muted mb-0">Configuration du système</p>
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
        <!-- Paramètres généraux -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Paramètres de l'Établissement
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="settingsForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_settings">

                        <div class="mb-3">
                            <label for="nom_etablissement" class="form-label">Nom de l'établissement *</label>
                            <input type="text" class="form-control" id="nom_etablissement" name="nom_etablissement" 
                                   value="<?php echo htmlspecialchars($settings['nom_etablissement'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="adresse_etablissement" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse_etablissement" name="adresse_etablissement" rows="3"><?php echo htmlspecialchars($settings['adresse_etablissement'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone_etablissement" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone_etablissement" name="telephone_etablissement" 
                                           value="<?php echo htmlspecialchars($settings['telephone_etablissement'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_etablissement" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_etablissement" name="email_etablissement" 
                                           value="<?php echo htmlspecialchars($settings['email_etablissement'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee_scolaire" class="form-label">Année Scolaire</label>
                                    <input type="number" class="form-control" id="annee_scolaire" name="annee_scolaire" 
                                           value="<?php echo $settings['annee_scolaire'] ?? date('Y'); ?>" min="2000" max="2100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semestre_actuel" class="form-label">Semestre Actuel</label>
                                    <select class="form-select" id="semestre_actuel" name="semestre_actuel">
                                        <option value="1" <?php echo ($settings['semestre_actuel'] ?? '1') === '1' ? 'selected' : ''; ?>>Semestre 1</option>
                                        <option value="2" <?php echo ($settings['semestre_actuel'] ?? '1') === '2' ? 'selected' : ''; ?>>Semestre 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="note_minimale" class="form-label">Note Minimale</label>
                                    <input type="number" class="form-control" id="note_minimale" name="note_minimale" 
                                           value="<?php echo $settings['note_minimale'] ?? '10'; ?>" min="0" max="20">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="note_maximale" class="form-label">Note Maximale</label>
                                    <input type="number" class="form-control" id="note_maximale" name="note_maximale" 
                                           value="<?php echo $settings['note_maximale'] ?? '20'; ?>" min="0" max="20">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="delai_inscription" class="form-label">Délai d'inscription (jours)</label>
                                    <input type="number" class="form-control" id="delai_inscription" name="delai_inscription" 
                                           value="<?php echo $settings['delai_inscription'] ?? '30'; ?>" min="1" max="365">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activer_notifications" name="activer_notifications" value="1"
                                               <?php echo ($settings['activer_notifications'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activer_notifications">
                                            Activer les notifications
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activer_maintenance" name="activer_maintenance" value="1"
                                               <?php echo ($settings['activer_maintenance'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activer_maintenance">
                                            Mode maintenance
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sauvegarde -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Sauvegarde de la Base de Données
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Créez une sauvegarde complète de la base de données.</p>
                    
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="backup_database">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Créer une sauvegarde de la base de données ?')">
                            <i class="fas fa-download me-2"></i>Créer une sauvegarde
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-server me-2"></i>Informations Système
                    </h5>
                </div>
                <div class="card-body">
                    <?php $systemInfo = getSystemInfo(); ?>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Version PHP</span>
                            <span class="badge bg-primary"><?php echo $systemInfo['php_version']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Base de données</span>
                            <span class="badge bg-info"><?php echo $systemInfo['mysql_version']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Serveur</span>
                            <span class="text-muted small"><?php echo $systemInfo['server_software']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Espace disque libre</span>
                            <span class="text-muted"><?php echo $systemInfo['disk_free_space']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Espace disque total</span>
                            <span class="text-muted"><?php echo $systemInfo['disk_total_space']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Limite mémoire</span>
                            <span class="text-muted"><?php echo $systemInfo['memory_limit']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Temps d'exécution max</span>
                            <span class="text-muted"><?php echo $systemInfo['max_execution_time']; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Taille upload max</span>
                            <span class="text-muted"><?php echo $systemInfo['upload_max_filesize']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-primary"><?php echo getCount('etudiants'); ?></h3>
                                <small class="text-muted">Étudiants</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-success"><?php echo getCount('enseignants'); ?></h3>
                                <small class="text-muted">Enseignants</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-info"><?php echo getCount('classes'); ?></h3>
                                <small class="text-muted">Classes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-warning"><?php echo getCount('matieres'); ?></h3>
                                <small class="text-muted">Matières</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 15px 0;
}

.stat-item h3 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>

<script>
// Validation du formulaire
document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
    const requiredFields = ['nom_etablissement'];
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

<?php
function getCount($table) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE actif = 1");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

require_once dirname(__DIR__) . '/includes/footer.php'; 
?> 