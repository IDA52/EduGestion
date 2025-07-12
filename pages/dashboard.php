<?php
require_once dirname(__DIR__) . '/config.php';
if (!isAuthenticated()) {
    redirect('login');
}
$pageTitle = "Tableau de bord";
require_once dirname(__DIR__) . '/includes/header.php';

// Récupération des statistiques
//$stats = getDashboardStats();
//$recentActivities = getRecentActivities();
//$upcomingEvents = getUpcomingEvents();
?>

<div class="container-fluid">
    <!-- En-tête du tableau de bord -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <div class="dashboard-title">
                        <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                    </div>
                    <p class="dashboard-welcome">Bienvenue, <?php echo $_SESSION['user_name']; ?> !</p>
                </div>
                <div class="dashboard-clock text-end">
                    <i class="fas fa-clock me-1"></i>
                    <span><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php if (hasPermission('enseignant') && !hasPermission('admin')): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info text-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                Bienvenue sur votre espace personnel. Utilisez le menu en haut à droite pour accéder à votre profil ou vous déconnecter.
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (hasPermission('admin')): ?>
    <?php
    $pdo = getDBConnection();
    $total_etudiants = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE actif = 1")->fetchColumn();
    $total_enseignants = $pdo->query("SELECT COUNT(*) FROM enseignants WHERE actif = 1")->fetchColumn();
    $total_modules = $pdo->query("SELECT COUNT(*) FROM modules WHERE actif = 1")->fetchColumn();
    $moyenne_generale = $pdo->query("SELECT AVG(note) FROM notes")->fetchColumn();
    $moyenne_generale = $moyenne_generale ? number_format($moyenne_generale, 2) : '0.00';
    // Section activités récentes
    $logs = $pdo->query("SELECT * FROM logs ORDER BY created_at DESC LIMIT 10")->fetchAll();
    ?>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_etudiants; ?></div>
                        <div>Étudiants</div>
                    </div>
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_enseignants; ?></div>
                        <div>Enseignants</div>
                    </div>
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_modules; ?></div>
                        <div>Modules</div>
                    </div>
                    <i class="fas fa-book fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $moyenne_generale; ?></div>
                        <div>Moyenne générale</div>
                    </div>
                    <i class="fas fa-star fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-history me-2"></i>Activités récentes
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($logs)): ?>
                            <li class="list-group-item text-muted">Aucune activité récente.</li>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?php echo htmlspecialchars($log['action'] ?? ''); ?></strong>
                                    <?php if (!empty($log['details'])): ?>
                                        <span class="text-muted">- <?php echo htmlspecialchars($log['details']); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (hasPermission('etudiant') && !hasPermission('admin') && !hasPermission('enseignant')): ?>
    <?php
    $pdo = getDBConnection();
    
    // Initialiser toutes les variables
    $etudiant_id = null;
    $etudiant_nom = '';
    $etudiant_prenom = '';
    $classe_id = null;
    
    // Récupérer l'id de l'étudiant à partir de l'email utilisateur
    try {
        $stmt = $pdo->prepare("SELECT id, nom, prenom, classe_id FROM etudiants WHERE email = ? AND actif = 1");
        $stmt->execute([$_SESSION['user_email']]);
        $row = $stmt->fetch();
        if ($row) {
            $etudiant_id = $row['id'];
            $etudiant_nom = $row['nom'];
            $etudiant_prenom = $row['prenom'];
            $classe_id = $row['classe_id'];
        }
    } catch (PDOException $e) {
        // En cas d'erreur, les variables restent initialisées à null/vide
        error_log("Erreur lors de la récupération de l'étudiant: " . $e->getMessage());
    }
    
    // Récupérer les notes
    $notes = [];
    $moyenne_generale = 0;
    $total_notes = 0;
    if ($etudiant_id) {
        $stmt = $pdo->prepare("
            SELECT n.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
            FROM notes n
            LEFT JOIN modules m ON n.module_id = m.id
            LEFT JOIN enseignants ens ON n.enseignant_id = ens.id
            WHERE n.etudiant_id = ?
            ORDER BY n.annee_academique DESC, n.semestre, m.nom
        ");
        $stmt->execute([$etudiant_id]);
        $notes = $stmt->fetchAll();
        
        // Calculer la moyenne générale
        if (!empty($notes)) {
            $total_points = 0;
            $total_coefficients = 0;
            foreach ($notes as $note) {
                $total_points += $note['note'] * $note['coefficient'];
                $total_coefficients += $note['coefficient'];
            }
            if ($total_coefficients > 0) {
                $moyenne_generale = number_format($total_points / $total_coefficients, 2);
            }
            $total_notes = count($notes);
        }
    }
    
    // Récupérer l'emploi du temps de la classe
    $emploi_temps = [];
    if ($classe_id) {
        try {
            $stmt = $pdo->prepare("
                SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
                FROM emplois_du_temps e
                LEFT JOIN modules m ON e.module_id = m.id
                LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
                WHERE e.classe_id = ? AND e.actif = 1
                ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
            ");
            $stmt->execute([$classe_id]);
            $emploi_temps = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Si la table n'a pas de colonne classe_id, on récupère tous les emplois
            $stmt = $pdo->query("
                SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
                FROM emplois_du_temps e
                LEFT JOIN modules m ON e.module_id = m.id
                LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
                WHERE e.actif = 1
                ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
                LIMIT 10
            ");
            $emploi_temps = $stmt->fetchAll();
        }
    } else {
        // Si pas de classe_id, récupérer tous les emplois
        try {
            $stmt = $pdo->query("
                SELECT e.*, m.nom as module_nom, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom
                FROM emplois_du_temps e
                LEFT JOIN modules m ON e.module_id = m.id
                LEFT JOIN enseignants ens ON e.enseignant_id = ens.id
                WHERE e.actif = 1
                ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
                LIMIT 10
            ");
            $emploi_temps = $stmt->fetchAll();
        } catch (PDOException $e) {
            // En cas d'erreur, emploi_temps reste un tableau vide
            error_log("Erreur lors de la récupération de l'emploi du temps: " . $e->getMessage());
        }
    }
    
    // Déterminer le prochain cours
    $prochain_cours = null;
    $jours = ['Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6];
    $jour_actuel = date('N'); // 1 (Lundi) à 7 (Dimanche)
    $heure_actuelle = date('H:i');
    
    foreach ($emploi_temps as $cours) {
        $jour_cours = $jours[$cours['jour']] ?? 0;
        if ($jour_cours >= $jour_actuel) {
            if ($jour_cours == $jour_actuel && $cours['heure_debut'] > $heure_actuelle) {
                $prochain_cours = $cours;
                break;
            } elseif ($jour_cours > $jour_actuel) {
                $prochain_cours = $cours;
                break;
            }
        }
    }
    ?>
    
    <!-- Statistiques de l'étudiant -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_notes; ?></div>
                        <div>Notes reçues</div>
                    </div>
                    <i class="fas fa-star fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $moyenne_generale; ?></div>
                        <div>Moyenne générale</div>
                    </div>
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo count($emploi_temps); ?></div>
                        <div>Cours cette semaine</div>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo count(array_filter($notes, function($n) { return $n['note'] < 10; })); ?></div>
                        <div>Notes < 10</div>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions rapides pour étudiants -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/emploi_temps" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                <span>Voir mon emploi du temps</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/profil" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-user-edit fa-2x mb-2"></i>
                                <span>Modifier mon profil</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" data-bs-toggle="modal" data-bs-target="#reclamationModal">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <span>Faire une réclamation</span>
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/logout" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                                <span>Se déconnecter</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Prochain cours -->
    <?php if ($prochain_cours): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock me-3 fa-2x"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Prochain cours</h5>
                        <p class="mb-0">
                            <strong><?php echo htmlspecialchars($prochain_cours['module_nom']); ?></strong> 
                            avec <?php echo htmlspecialchars($prochain_cours['enseignant_nom'] . ' ' . $prochain_cours['enseignant_prenom']); ?>
                            <br>
                            <small class="text-muted">
                                <?php echo $prochain_cours['jour']; ?> de <?php echo substr($prochain_cours['heure_debut'], 0, 5); ?> 
                                à <?php echo substr($prochain_cours['heure_fin'], 0, 5); ?> - Salle <?php echo htmlspecialchars($prochain_cours['salle']); ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Mes Notes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-star me-2"></i>Mes Notes
                    </div>
                    <div>
                        <span class="badge bg-light text-dark">Moyenne: <?php echo $moyenne_generale; ?>/20</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Coef.</th>
                                    <th>Semestre</th>
                                    <th>Année</th>
                                    <th>Enseignant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notes as $n): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($n['module_nom'] ?? ''); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($n['type_evaluation'] ?? ''); ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $note_class = $n['note'] >= 10 ? 'bg-success' : 'bg-danger';
                                        echo "<span class='badge $note_class fs-6'>" . $n['note'] . "/20</span>";
                                        ?>
                                    </td>
                                    <td><?php echo $n['coefficient']; ?></td>
                                    <td><?php echo htmlspecialchars($n['semestre'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($n['annee_academique'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($n['enseignant_nom'] ?? '') . ' ' . ($n['enseignant_prenom'] ?? '')); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#reclamationModal" data-note-id="<?php echo $n['id']; ?>" data-module="<?php echo htmlspecialchars($n['module_nom'] ?? ''); ?>" data-type="<?php echo htmlspecialchars($n['type_evaluation'] ?? ''); ?>" data-note="<?php echo $n['note']; ?>">
                                            <i class="fas fa-exclamation-circle"></i> Réclamer
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($notes)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i><br>
                                    Aucune note disponible pour le moment.
                                </td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de réclamation -->
    <div class="modal fade" id="reclamationModal" tabindex="-1" aria-labelledby="reclamationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="" id="reclamationForm">
            <div class="modal-header">
              <h5 class="modal-title" id="reclamationModalLabel">Faire une réclamation sur la note</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="note_id" id="reclamation_note_id">
              <div class="mb-2">
                <strong>Module :</strong> <span id="reclamation_module"></span><br>
                <strong>Type :</strong> <span id="reclamation_type"></span><br>
                <strong>Note :</strong> <span id="reclamation_note"></span>
              </div>
              <div class="mb-3">
                <label for="motif" class="form-label">Motif de la réclamation *</label>
                <input type="text" class="form-control" id="motif" name="motif" required>
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-primary">Envoyer la réclamation</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
    // Pré-remplir le modal avec les infos de la note
    var reclamationModal = document.getElementById('reclamationModal');
    reclamationModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('reclamation_note_id').value = button.getAttribute('data-note-id');
      document.getElementById('reclamation_module').textContent = button.getAttribute('data-module');
      document.getElementById('reclamation_type').textContent = button.getAttribute('data-type');
      document.getElementById('reclamation_note').textContent = button.getAttribute('data-note');
    });
    </script>
    <?php endif; ?>
    <!-- Tableau de bord épuré : plus d'actions rapides, statistiques ou activités récentes. -->
</div>

<style>
body.dashboard-bg {
    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('dashboard-bg');
});
</script>

<?php
// Les fonctions getDashboardStats, getRecentActivities, getUpcomingEvents, formatDate sont conservées en bas du fichier si besoin ailleurs.
require_once dirname(__DIR__) . '/includes/footer.php';
?> 