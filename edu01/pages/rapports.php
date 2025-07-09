<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Rapports et Statistiques";
require_once dirname(__DIR__) . '/includes/header.php';

// Vérification des permissions
if (!hasPermission('admin')) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'danger';
    redirect('dashboard');
}

// Récupération des statistiques
$stats = getStatistics();
$recentActivities = getRecentActivities();
$topStudents = getTopStudents();
$classStats = getClassStatistics();

// Fonctions
function getStatistics() {
    try {
        $pdo = getDBConnection();
        
        $stats = [];
        
        // Nombre total d'étudiants
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM etudiants WHERE actif = 1");
        $stats['total_etudiants'] = $stmt->fetch()['count'];
        
        // Nombre total d'enseignants
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM enseignants WHERE actif = 1");
        $stats['total_enseignants'] = $stmt->fetch()['count'];
        
        // Nombre total de modules (au lieu de classes)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM modules WHERE actif = 1");
        $stats['total_classes'] = $stmt->fetch()['count'];
        
        // Nombre total de modules
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM modules WHERE actif = 1");
        $stats['total_matieres'] = $stmt->fetch()['count'];
        
        // Nombre total de notes
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM notes");
        $stats['total_notes'] = $stmt->fetch()['count'];
        
        // Moyenne générale
        $stmt = $pdo->query("SELECT AVG(note) as moyenne FROM notes");
        $result = $stmt->fetch();
        $stats['moyenne_generale'] = $result['moyenne'] ? round($result['moyenne'], 2) : 0;
        
        return $stats;
    } catch (PDOException $e) {
        return [
            'total_etudiants' => 0,
            'total_enseignants' => 0,
            'total_classes' => 0,
            'total_matieres' => 0,
            'total_notes' => 0,
            'moyenne_generale' => 0
        ];
    }
}

function getRecentActivities() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT * FROM logs 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getTopStudents() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT e.nom, e.prenom, e.matricule, AVG(n.note) as moyenne
            FROM etudiants e 
            LEFT JOIN notes n ON e.id = n.etudiant_id
            WHERE e.actif = 1 
            GROUP BY e.id 
            HAVING moyenne IS NOT NULL
            ORDER BY moyenne DESC 
            LIMIT 10
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getClassStatistics() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT e.filiere as classe_nom, 
                   COUNT(e.id) as nb_etudiants,
                   AVG(n.note) as moyenne_classe
            FROM etudiants e 
            LEFT JOIN notes n ON e.id = n.etudiant_id
            WHERE e.actif = 1 
            GROUP BY e.filiere 
            ORDER BY e.filiere
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getMatiereStatistics() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT m.nom as matiere_nom, 
                   COUNT(n.id) as nb_notes,
                   AVG(n.note) as moyenne_matiere,
                   MIN(n.note) as note_min,
                   MAX(n.note) as note_max
            FROM modules m 
            LEFT JOIN notes n ON m.id = n.module_id
            WHERE m.actif = 1 
            GROUP BY m.id 
            ORDER BY moyenne_matiere DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

$matiereStats = getMatiereStatistics();
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line me-2"></i>Rapports et Statistiques
                    </h1>
                    <p class="text-muted mb-0">Analyse des données académiques</p>
                </div>
                <div>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Étudiants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['total_etudiants'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Enseignants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['total_enseignants'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Modules
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['total_classes'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Moyenne Générale
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['moyenne_generale'] ?? 0; ?>/20
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Meilleurs étudiants -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>Top 10 des Meilleurs Étudiants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Étudiant</th>
                                    <th>Moyenne</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topStudents)): ?>
                                    <?php foreach ($topStudents as $index => $student): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index < 3): ?>
                                                <span class="badge bg-warning"><?php echo $index + 1; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo $index + 1; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($student['matricule']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6"><?php echo round($student['moyenne'], 2); ?>/20</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>Aucun étudiant avec des notes trouvé
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques par classe -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques par Classe
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Étudiants</th>
                                    <th>Moyenne</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($classStats)): ?>
                                    <?php foreach ($classStats as $class): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?php echo htmlspecialchars($class['classe_nom']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $class['nb_etudiants']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($class['moyenne_classe']): ?>
                                                <span class="badge bg-success"><?php echo round($class['moyenne_classe'], 2); ?>/20</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>Aucune donnée de classe disponible
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par matière -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>Statistiques par Matière
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Nombre de notes</th>
                                    <th>Moyenne</th>
                                    <th>Note min</th>
                                    <th>Note max</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($matiereStats)): ?>
                                    <?php foreach ($matiereStats as $matiere): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?php echo htmlspecialchars($matiere['matiere_nom']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $matiere['nb_notes']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($matiere['moyenne_matiere']): ?>
                                                <span class="badge bg-success"><?php echo round($matiere['moyenne_matiere'], 2); ?>/20</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($matiere['note_min']): ?>
                                                <span class="badge bg-danger"><?php echo $matiere['note_min']; ?>/20</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($matiere['note_max']): ?>
                                                <span class="badge bg-success"><?php echo $matiere['note_max']; ?>/20</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>Aucune donnée de matière disponible
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Activités Récentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if (!empty($recentActivities)): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title"><?php echo htmlspecialchars($activity['action']); ?></h6>
                                    <p class="timeline-text"><?php echo htmlspecialchars($activity['details'] ?? 'Aucun détail disponible'); ?></p>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p>Aucune activité récente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
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

@media print {
    .btn, .card-header .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 