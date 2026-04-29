<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ETUDIANT);
$tm = new TicketModel();
$mm = new MenuModel();
$tm->expirerTickets();

// Traitement POST → Controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new TicketController();
    $ctrl->reserver();
}

$menuDuJour  = $mm->getMenuDuJour();
$nbTickets   = $tm->countTicketsDuJour();
$attente     = estimerAttente($nbTickets);
$dejaReserve = $menuDuJour && $tm->aDejaTicketAujourdhui((int)$_SESSION['user_id']);
$pageTitle   = 'Réserver'; $activeNav = 'dashboard';
$err         = flash('error');
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">

    <div class="page-hero">
      <h2 class="mb-1"><i class="bi bi-ticket me-2"></i>Réserver un repas</h2>
      <p class="opacity-75 mb-0 small">
        Menu du <?= date('d/m/Y') ?> · Prix fixe : <strong><?= formatPrix(MENU_PRIX_FIXE) ?></strong>
      </p>
    </div>

    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>
    <?php if ($err): ?>
      <div class="alert alert-danger py-2 small mb-3 auto-dismiss d-flex gap-2 align-items-center">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <?= h($err) ?>
        <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($dejaReserve): ?>
      <div class="alert alert-warning">
        <i class="bi bi-info-circle me-2"></i>
        Vous avez déjà réservé aujourd'hui.
        <a href="<?= APP_URL ?>/views/student/tickets.php" class="fw-bold">Voir mes tickets →</a>
      </div>

    <?php elseif (!$menuDuJour): ?>
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="fs-1 mb-2">😔</div>
        <h5>Aucun menu disponible aujourd'hui</h5>
        <p class="text-muted small">L'administrateur n'a pas encore publié le menu du jour.</p>
        <a href="<?= APP_URL ?>/views/student/dashboard.php" class="btn btn-primary mt-2">Retour au tableau de bord</a>
      </div>

    <?php else: ?>
      <div class="row g-4 align-items-start">

        <!-- Menu + attente -->
        <div class="col-lg-6">
          <?php include APP_ROOT.'/views/partials/menu_card.php'; ?>
          <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
            <div class="wait-pill"><span class="wait-dot"></span>Attente : <strong><?= $attente ?></strong></div>
            <span class="text-muted small"><?= $nbTickets ?> rés. aujourd'hui</span>
          </div>
          <div class="alert alert-warning py-2 small mt-3">
            <i class="bi bi-clock me-1"></i>Ce ticket expire aujourd'hui (<?= date('d/m/Y') ?>) à minuit s'il n'est pas utilisé.
          </div>
        </div>

        <!-- Formulaire paiement -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold small">
              <i class="bi bi-credit-card me-2 text-primary"></i>Mode de paiement
            </div>
            <div class="card-body">
              <form method="POST" action="<?= APP_URL ?>/views/student/reservation.php" id="form-res">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <div class="row g-3 mb-3">
                  <div class="col-6 pay-opt">
                    <input type="radio" name="mode_paiement" id="especes" value="especes" checked>
                    <label for="especes">
                      <span class="pi">💵</span>
                      <span class="pn">Espèces</span>
                      <span class="pd">Paiement à la caisse</span>
                    </label>
                  </div>
                  <div class="col-6 pay-opt">
                    <input type="radio" name="mode_paiement" id="d17" value="d17">
                    <label for="d17">
                      <span class="pi">📱</span>
                      <span class="pn">D17</span>
                      <span class="pd">Paiement mobile (simulation)</span>
                    </label>
                  </div>
                </div>

                <div id="d17-info" class="alert alert-info py-2 small mb-3 d-none">
                  <i class="bi bi-phone me-1"></i>
                  <strong>Simulation D17</strong> — Une transaction fictive sera enregistrée.
                </div>

                <!-- Récapitulatif -->
                <div class="rounded p-3 mb-3 small" style="background:var(--c-cream)">
                  <div class="d-flex justify-content-between mb-1">
                    <span>Menu du jour</span>
                    <span class="fw-semibold"><?= formatPrix(MENU_PRIX_FIXE) ?></span>
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between">
                    <strong>Total à payer</strong>
                    <strong class="text-primary"><?= formatPrix(MENU_PRIX_FIXE) ?></strong>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                  <i class="bi bi-check-circle me-1"></i>Confirmer (<?= formatPrix(MENU_PRIX_FIXE) ?>)
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.querySelectorAll('input[name="mode_paiement"]').forEach(r => {
  r.addEventListener('change', () => {
    document.getElementById('d17-info').classList.toggle('d-none', r.value !== 'd17');
  });
});
</script>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
