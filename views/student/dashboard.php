<?php
// ============================================================
//  views/student/dashboard.php — Tableau de bord étudiant
//  Graphiques CanvasJS — données injectées directement en PHP
// ============================================================
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ETUDIANT);
$pageTitle = 'Mon espace'; $activeNav = 'dashboard';
$tm = new TicketModel(); $tm->expirerTickets();
$mm = new MenuModel();
$user       = currentUser();
$uid        = (int)$user['id'];
$stats      = $tm->getStatsEtudiant($uid);
$recent     = array_slice($tm->getByEtudiant($uid), 0, 5);
$menuDuJour = $mm->getMenuDuJour();
$nbTickets  = $tm->countTicketsDuJour();
$attente    = estimerAttente($nbTickets);

// Données pour les graphiques — injectées directement en JS
$kpiData = [
    ['label' => 'Total réservations', 'y' => (int)$stats['total'],    'color' => '#C8553D'],
    ['label' => 'Tickets utilisés',   'y' => (int)$stats['utilises'], 'color' => '#3A5A40'],
    ['label' => 'Tickets valides',    'y' => (int)$stats['valides'],  'color' => '#D4A853'],
    ['label' => 'Annulés',            'y' => (int)$stats['annules'],  'color' => '#7A7A7A'],
];
$depensesData  = $tm->getDepensesEtudiantParJour($uid, 30);
$depenseTotale = round((float)$stats['depense'], 3);

include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
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

    <!-- GRAPHIQUES -->
    <div class="row g-4 mb-4">

      <!-- Column Chart -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
            <span class="fw-bold small">
              <i class="bi bi-bar-chart-fill me-2" style="color:#C8553D"></i>Mes tickets
            </span>
            <p class="text-muted mb-0" style="font-size:.73rem">Répartition par statut</p>
          </div>
          <div class="card-body px-2 pt-2 pb-3">
            <div id="chartColumn" style="height:260px;width:100%"></div>
          </div>
        </div>
      </div>

      <!-- Area Chart -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
            <span class="fw-bold small">
              <i class="bi bi-graph-up me-2" style="color:#3A5A40"></i>Mes dépenses
            </span>
            <p class="text-muted mb-0" style="font-size:.73rem">Évolution sur 30 jours (DT)</p>
          </div>
          <div class="card-body px-2 pt-2 pb-3">
            <div id="chartArea" style="height:260px;width:100%"></div>
          </div>
        </div>
      </div>

    </div>

    <!-- Tickets récents + Menu du jour -->
    <div class="row g-4">
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

<!-- CanvasJS -->
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>
// Données injectées directement depuis PHP
const kpiData      = <?= json_encode($kpiData,      JSON_UNESCAPED_UNICODE) ?>;
const depensesData = <?= json_encode($depensesData, JSON_UNESCAPED_UNICODE) ?>;
const depenseTot   = <?= json_encode($depenseTotale) ?>;

// ── Column Chart ─────────────────────────────────────────────
new CanvasJS.Chart('chartColumn', {
  animationEnabled: true,
  animationDuration: 600,
  backgroundColor: 'transparent',
  title: { text: '' },
  axisX: {
    tickLength: 0, lineThickness: 0,
    labelFontFamily: 'DM Sans, sans-serif',
    labelFontSize: 12, labelFontColor: '#7A7A7A'
  },
  axisY: {
    gridThickness: 1, gridColor: '#F0E6D0',
    lineThickness: 0, tickLength: 0,
    labelFontFamily: 'DM Sans, sans-serif',
    labelFontSize: 11, labelFontColor: '#7A7A7A',
    minimum: 0, interval: 1
  },
  toolTip: { borderThickness: 0, cornerRadius: 8, fontFamily: 'DM Sans, sans-serif', fontSize: 13 },
  data: [{
    type: 'column',
    cornerRadius: 6,
    indexLabelFontFamily: 'DM Sans, sans-serif',
    indexLabelFontSize: 13,
    indexLabelFontWeight: 700,
    indexLabelPlacement: 'outside',
    indexLabelFontColor: '#1C1C1E',
    dataPoints: kpiData.map(k => ({
      label: k.label,
      y: k.y,
      color: k.color,
      indexLabel: k.y > 0 ? String(k.y) : ''
    }))
  }]
}).render();

// ── Area Chart ───────────────────────────────────────────────
const areaPoints = depensesData.length > 0
  ? depensesData.map(d => ({ x: new Date(d.x), y: d.y }))
  : [{ x: new Date(), y: 0 }];

new CanvasJS.Chart('chartArea', {
  animationEnabled: true,
  animationDuration: 800,
  backgroundColor: 'transparent',
  title: { text: '' },
  subtitles: [{
    text: 'Total : ' + depenseTot.toFixed(3) + ' DT',
    fontFamily: 'Playfair Display, serif',
    fontSize: 14, fontColor: '#3A5A40', fontWeight: 'bold',
    horizontalAlign: 'right', dockInsidePlotArea: true
  }],
  axisX: {
    valueFormatString: 'DD/MM',
    labelFontFamily: 'DM Sans, sans-serif',
    labelFontSize: 11, labelFontColor: '#7A7A7A',
    gridThickness: 0, lineColor: '#E4D8C8',
    tickLength: 4, tickColor: '#E4D8C8'
  },
  axisY: {
    suffix: ' DT',
    gridThickness: 1, gridColor: '#F0E6D0',
    lineThickness: 0, tickLength: 0,
    labelFontFamily: 'DM Sans, sans-serif',
    labelFontSize: 11, labelFontColor: '#7A7A7A',
    minimum: 0
  },
  toolTip: {
    borderThickness: 0, cornerRadius: 8,
    fontFamily: 'DM Sans, sans-serif', fontSize: 13,
    contentFormatter: function(e) {
      const dp = e.entries[0].dataPoint;
      const dateStr = new Date(dp.x).toLocaleDateString('fr-FR', {day:'2-digit', month:'short'});
      return '<strong>' + dateStr + '</strong> : ' + parseFloat(dp.y).toFixed(3) + ' DT';
    }
  },
  data: [{
    type: 'area',
    lineThickness: 2.5, lineColor: '#3A5A40',
    color: 'rgba(58,90,64,0.12)',
    markerSize: 6, markerColor: '#3A5A40',
    markerBorderColor: '#fff', markerBorderThickness: 2,
    xValueType: 'dateTime',
    dataPoints: areaPoints
  }]
}).render();
</script>

<?php include APP_ROOT.'/views/layouts/footer.php'; ?>