<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ETUDIANT);
$pageTitle='Mon espace'; $activeNav='dashboard';
$tm = new TicketModel(); $tm->expirerTickets();
$mm = new MenuModel();
$user       = currentUser();
$stats      = $tm->getStatsEtudiant((int)$user['id']);
$recent     = array_slice($tm->getByEtudiant((int)$user['id']), 0, 5);
$menuDuJour = $mm->getMenuDuJour();
$nbTickets  = $tm->countTicketsDuJour();
$attente    = estimerAttente($nbTickets);
include APP_ROOT.'/views/layout.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/sidebar.php'; ?>
  <div class="dashboard-main w-100">

    <!-- Hero -->
    <div class="page-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="mb-1">Bonjour, <?= h(explode(' ', $user['nom'] ?? '')[0]) ?> 👋</h2>
        <p class="opacity-75 mb-0 small"><?= date('l d F Y') ?></p>
      </div>
      <?php if ($menuDuJour): ?>
        <a href="<?= APP_URL ?>/views/student/reservation.php" class="btn btn-warning fw-bold">
          <i class="bi bi-ticket me-1"></i>Réserver (<?= formatPrix(MENU_PRIX_FIXE) ?>)
        </a>
      <?php endif; ?>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-1">🎫</div><div><div class="kpi-label">Total réservations</div><div class="kpi-value"><?= (int)$stats['total'] ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-2">✅</div><div><div class="kpi-label">Tickets utilisés</div><div class="kpi-value"><?= (int)$stats['utilises'] ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-3">💰</div><div><div class="kpi-label">Total dépensé</div><div class="kpi-value" style="font-size:1.05rem"><?= formatPrix($stats['depense']) ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-4">🔖</div><div><div class="kpi-label">Tickets valides</div><div class="kpi-value"><?= (int)$stats['valides'] ?></div></div></div></div>
    </div>

    <div class="row g-4">
      <!-- Tickets récents -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-bold small"><i class="bi bi-collection me-2 text-primary"></i>Derniers tickets</span>
            <a href="<?= APP_URL ?>/views/student/tickets.php" class="btn btn-sm btn-outline-primary py-0">Voir tout</a>
          </div>
          <?php if (empty($recent)): ?>
            <div class="text-center text-muted py-4 small">Aucune réservation pour le moment.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Date</th><th>Montant</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                  <?php foreach ($recent as $t): ?>
                    <tr>
                      <td class="text-muted small">#<?= $t['id_ticket'] ?></td>
                      <td class="small"><?= date('d/m/Y', strtotime($t['dateMenu'])) ?></td>
                      <td class="fw-bold small"><?= formatPrix((float)$t['montantTotal']) ?></td>
                      <td><span class="sbadge s-<?= $t['status'] ?>"><span class="sbadge-dot"></span><?= $t['status'] ?></span></td>
                      <td><a href="<?= APP_URL ?>/views/student/ticket_view.php?id=<?= $t['id_ticket'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2">→</a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Menu du jour -->
      <div class="col-lg-5">
        <?php if ($menuDuJour): ?>
          <?php include APP_ROOT.'/views/partials/menu_card.php'; ?>
          <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
            <div class="wait-pill"><span class="wait-dot"></span><?= $attente ?></div>
            <span class="text-muted small"><?= $nbTickets ?> rés.</span>
          </div>
          <a href="<?= APP_URL ?>/views/student/reservation.php" class="btn btn-primary w-100 mt-2">
            <i class="bi bi-ticket me-1"></i>Réserver maintenant
          </a>
        <?php else: ?>
          <div class="card border-0 shadow-sm text-center py-4">
            <div class="fs-2 mb-2">🕐</div>
            <h6>Menu en attente</h6>
            <p class="text-muted small mb-0">Pas encore de menu aujourd'hui.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
<?php include APP_ROOT.'/views/footer.php'; ?>
