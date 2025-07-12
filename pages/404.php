<?php
$pageTitle = "Page non trouvée";
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-lg">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 text-danger mb-3">404</h1>
                    <h2 class="h4 text-muted mb-4">Page non trouvée</h2>
                    
                    <p class="lead mb-4">
                        La page que vous recherchez n'existe pas ou a été déplacée.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?php echo APP_URL; ?>" class="btn btn-primary me-md-2">
                            <i class="fas fa-home me-2"></i>Accueil
                        </a>
                        <a href="<?php echo APP_URL; ?>/dashboard" class="btn btn-outline-secondary">
                            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?> 