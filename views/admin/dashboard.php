<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$tm  = new TicketModel(); $mm = new MenuModel(); $um = new UserModel();
$tm->expirerTickets();
$stats       = $tm->getStatsAdmin();
$nbEtudiants = $um->countEtudiants();
$menuDuJour  = $mm->getMenuDuJour();
$pageTitle   = 'Tableau de bord Admin'; $activeNav = 'admin';
include APP_ROOT.'/views/layout.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/sidebar.php'; ?>
  <div class="dashboard-main w-100">

    <div class="page-hero d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h2 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</h2>
        <p class="opacity-75 mb-0 small"><?= date('l d F Y') ?></p>
      </div>
      <?php if (!$menuDuJour): ?>
        <a href="<?= APP_URL ?>/views/admin/menu.php" class="btn btn-warning fw-bold">
          <i class="bi bi-journal-plus me-1"></i>Saisir le menu du jour
        </a>
      <?php else: ?>
        <span class="badge bg-success px-3 py-2" style="font-size:.82rem">
          <i class="bi bi-check-circle me-1"></i>Menu publié · <?= formatPrix(MENU_PRIX_FIXE) ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-1">🎫</div><div><div class="kpi-label">Tickets aujourd'hui</div><div class="kpi-value"><?= (int)$stats['tickets_jour'] ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-3">💰</div><div><div class="kpi-label">Recette du jour</div><div class="kpi-value" style="font-size:1rem"><?= formatPrix((float)$stats['recette_jour']) ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-2">👩‍🎓</div><div><div class="kpi-label">Étudiants inscrits</div><div class="kpi-value"><?= $nbEtudiants ?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-4">📊</div><div><div class="kpi-label">Recette totale</div><div class="kpi-value" style="font-size:1rem"><?= formatPrix((float)$stats['recette_totale']) ?></div></div></div></div>
    </div>

    <div class="row g-4">
      <!-- Graphique fréquentation -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small">
            <i class="bi bi-graph-up me-2 text-primary"></i>Jours les plus fréquentés
          </div>
          <div class="card-body p-0">
            <?php if (empty($stats['top_jours'])): ?>
              <div class="text-center text-muted py-4 small">Pas encore de données.</div>
            <?php else:
              $max = max(array_column($stats['top_jours'], 'nb'));
              foreach ($stats['top_jours'] as $j): ?>
                <div class="px-3 py-2" style="border-bottom:1px solid var(--c-border)">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold small"><?= date('d/m/Y', strtotime($j['dateMenu'])) ?></span>
                    <span class="fw-bold small text-primary"><?= $j['nb'] ?> tickets</span>
                  </div>
                  <div class="progress" style="height:5px">
                    <div class="progress-bar" style="width:<?= $max > 0 ? round($j['nb']/$max*100) : 0 ?>%"></div>
                  </div>
                </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      </div>

      <!-- Actions rapides + menu -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-white fw-bold small">
            <i class="bi bi-lightning me-2 text-primary"></i>Actions rapides
          </div>
          <div class="card-body d-grid gap-2">
            <a href="<?= APP_URL ?>/views/admin/menu.php"    class="btn btn-primary btn-sm"><i class="bi bi-journal-plus me-1"></i>Saisir le menu du jour</a>
            <a href="<?= APP_URL ?>/views/admin/repas.php"   class="btn btn-outline-primary btn-sm"><i class="bi bi-egg-fried me-1"></i>Gérer les plats</a>
            <a href="<?= APP_URL ?>/views/admin/tickets.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-ticket-detailed me-1"></i>Tickets vendus</a>
            <a href="<?= APP_URL ?>/views/admin/users.php"   class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i>Utilisateurs</a>
          </div>
        </div>
        <?php if ($menuDuJour): ?>
          <?php include APP_ROOT.'/views/partials/menu_card.php'; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
<?php include APP_ROOT.'/views/footer.php'; ?>
