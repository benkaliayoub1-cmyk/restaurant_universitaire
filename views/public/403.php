<?php
require_once __DIR__ . '/../../index.php';
$pageTitle='Accès refusé';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex align-items-center justify-content-center flex-grow-1 py-5">
  <div class="text-center"><div style="font-size:5rem">🚫</div><h1 class="display-4 fw-bold mt-3">403</h1><p class="lead text-muted">Accès non autorisé.</p><a href="<?=APP_URL?>/views/public/home.php" class="btn btn-primary">Retour à l'accueil</a></div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
