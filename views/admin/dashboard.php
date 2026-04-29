<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$tm  = new TicketModel(); $mm = new MenuModel(); $um = new UserModel();
$tm->expirerTickets();
$stats          = $tm->getStatsAdmin();
$nbEtudiants    = $um->countEtudiants();
$menuDuJour     = $mm->getMenuDuJour();
$chartRecette   = $tm->getRecettesParJour(30);
$chartValidation= $tm->getStatsValidationDuJour();
$pageTitle      = 'Tableau de bord Admin'; $activeNav = 'admin';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">

    <!-- HERO -->
    <div class="page-hero d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div>
        <h2 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</h2>
        <p class="opacity-75 mb-0 small"><?= date('l d F Y') ?></p>
      </div>
      <?php if (!$menuDuJour): ?>
        <a href="<?= APP_URL ?>/views/admin/menu.php" class="btn btn-warning fw-bold">
          <i class="bi bi-journal-plus me-1"></i>Saisir le menu du jour
        </a>
      <?php else: ?>
        <span class="badge bg-success px-3 py-2" style="font-size:.85rem">
          <i class="bi bi-check-circle me-1"></i>Menu publié · <?= formatPrix(MENU_PRIX_FIXE) ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-icon kp-1">🎫</div>
          <div><div class="kpi-label">Tickets aujourd'hui</div><div class="kpi-value"><?= (int)$stats['tickets_jour'] ?></div></div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-icon kp-3">💰</div>
          <div><div class="kpi-label">Recette du jour</div><div class="kpi-value" style="font-size:1rem"><?= formatPrix((float)$stats['recette_jour']) ?></div></div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-icon kp-2">👩‍🎓</div>
          <div><div class="kpi-label">Étudiants inscrits</div><div class="kpi-value"><?= $nbEtudiants ?></div></div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-icon kp-4">📊</div>
          <div><div class="kpi-label">Recette totale</div><div class="kpi-value" style="font-size:1rem"><?= formatPrix((float)$stats['recette_totale']) ?></div></div>
        </div>
      </div>
    </div>

    <!-- LIGNE 2 : Donut + Fréquentation + Actions rapides -->
    <div class="row g-4 mb-4">

      <!-- Donut : Validation du jour -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white fw-bold small">
            <i class="bi bi-pie-chart me-2 text-primary"></i>Tickets du jour — Statut
          </div>
          <div class="card-body p-2">
            <?php if ($chartValidation['total'] === 0): ?>
              <div class="text-center text-muted py-4">
                <div class="fs-2 mb-2">🎫</div>
                <p class="small mb-0">Aucune réservation aujourd'hui.</p>
              </div>
            <?php else: ?>
              <div id="donutContainer" style="height:260px;width:100%;position:relative;"></div>
              <button class="btn btn-sm invisible" id="backButton" style="border-radius:4px;background:#C8553D;color:#fff;border:none;position:absolute;top:10px;right:10px;cursor:pointer;">&#8249; Retour</button>
            <?php endif; ?>
          </div>
          <?php if ($chartValidation['total'] > 0): ?>
          <div class="card-footer bg-white p-2">
            <div class="d-flex justify-content-around text-center">
              <div>
                <div class="fw-bold" style="color:#3A5A40"><?= $chartValidation['utilises'] ?></div>
                <div class="text-muted" style="font-size:.72rem">✅ Validés</div>
              </div>
              <div style="border-left:1px solid var(--c-border)"></div>
              <div>
                <div class="fw-bold text-primary"><?= $chartValidation['non_utilises'] ?></div>
                <div class="text-muted" style="font-size:.72rem">⏳ Non scannés</div>
              </div>
              <div style="border-left:1px solid var(--c-border)"></div>
              <div>
                <div class="fw-bold text-dark"><?= $chartValidation['total'] ?></div>
                <div class="text-muted" style="font-size:.72rem">📋 Total</div>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Fréquentation -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
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

      <!-- Actions rapides + Menu du jour -->
      <div class="col-lg-4">
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

    <!-- LIGNE 3 : Graphique recette journalière -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold small">
          <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
          Recette journalière — 30 derniers jours
        </span>
        <span class="text-muted small">en DT</span>
      </div>
      <div class="card-body p-3">
        <?php if (empty($chartRecette)): ?>
          <div class="text-center text-muted py-4 small">
            <div class="fs-3 mb-2">📊</div>Aucune donnée disponible.
          </div>
        <?php else: ?>
          <div id="chartContainer" style="height:320px;width:100%;"></div>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- fin dashboard-main -->
</div><!-- fin d-flex -->

<?php include APP_ROOT.'/views/layouts/footer.php'; ?>

<!-- ===== SCRIPTS CANVASJS ===== -->
<?php if (!empty($chartValidation['total']) || !empty($chartRecette)): ?>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<?php endif; ?>

<?php if ($chartValidation['total'] > 0): ?>
<script>
(function() {
  var totalJour    = <?= $chartValidation['total'] ?>;
  var donutData    = <?= json_encode($chartValidation['dataPoints'], JSON_NUMERIC_CHECK) ?>;

  // Options vue principale (donut)
  var donutOptions = {
    animationEnabled: true,
    theme: "light2",
    title: {
      text: "Statut tickets — " + new Date().toLocaleDateString('fr-FR'),
      fontFamily: "Playfair Display, Georgia, serif",
      fontColor: "#1C1C1E",
      fontSize: 14,
      fontWeight: "bold"
    },
    subtitles: [{
      text: "Cliquez pour voir le détail",
      backgroundColor: "#C8553D",
      fontSize: 12,
      fontColor: "white",
      padding: 4
    }],
    legend: {
      fontFamily: "DM Sans, sans-serif",
      fontSize: 12,
      itemTextFormatter: function(e) {
        var pct = totalJour > 0 ? Math.round(e.dataPoint.y / totalJour * 100) : 0;
        return e.dataPoint.name + " : " + e.dataPoint.y + " (" + pct + "%)";
      }
    },
    data: [{
      click: drilldownHandler,
      cursor: "pointer",
      explodeOnClick: false,
      innerRadius: "70%",
      legendMarkerType: "square",
      showInLegend: true,
      startAngle: 90,
      type: "doughnut",
      dataPoints: donutData
    }]
  };

  // Options vue drilldown (colonnes par statut)
  var drilldownOptions = {
    animationEnabled: true,
    theme: "light2",
    axisX: {
      labelFontColor: "#717171",
      lineColor: "#E4D8C8",
      tickColor: "#E4D8C8"
    },
    axisY: {
      gridThickness: 0,
      includeZero: true,
      labelFontColor: "#717171",
      lineColor: "#E4D8C8",
      tickColor: "#E4D8C8",
      lineThickness: 1,
      interval: 1
    },
    data: []
  };

  // Données drilldown pour chaque segment
  var drilldownData = {
    "Entrées validées": [{
      color: "#3A5A40",
      name: "Entrées validées",
      type: "column",
      dataPoints: [{ label: "Scannés ✅", y: <?= $chartValidation['utilises'] ?> }]
    }],
    "Réservés non scannés": [{
      color: "#C8553D",
      name: "Réservés non scannés",
      type: "column",
      dataPoints: [{ label: "Non scannés ⏳", y: <?= $chartValidation['non_utilises'] ?> }]
    }]
  };

  var donutChart = new CanvasJS.Chart("donutContainer", donutOptions);
  donutChart.render();

  function drilldownHandler(e) {
    var segmentName = e.dataPoint.name;
    if (!drilldownData[segmentName]) return;
    donutChart = new CanvasJS.Chart("donutContainer", drilldownOptions);
    donutChart.options.data  = drilldownData[segmentName];
    donutChart.options.title = {
      text: segmentName,
      fontFamily: "Playfair Display, Georgia, serif",
      fontColor: "#1C1C1E",
      fontSize: 14
    };
    donutChart.render();
    document.getElementById("backButton").classList.remove("invisible");
  }

  document.getElementById("backButton").addEventListener("click", function() {
    this.classList.add("invisible");
    donutChart = new CanvasJS.Chart("donutContainer", donutOptions);
    donutChart.render();
  });
})();
</script>
<?php endif; ?>

<?php if (!empty($chartRecette)): ?>
<script>
(function() {
  var dataPoints = <?= json_encode($chartRecette, JSON_NUMERIC_CHECK) ?>;

  var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    theme: "light2",
    title: {
      text: "Recette journalière du restaurant",
      fontFamily: "Playfair Display, Georgia, serif",
      fontColor: "#1C1C1E",
      fontSize: 16,
      fontWeight: "bold",
      padding: { bottom: 10 }
    },
    axisX: {
      valueFormatString: "DD/MM/YYYY",
      labelAngle: -30,
      labelFontSize: 11,
      gridThickness: 0,
      tickLength: 5,
      lineColor: "#E4D8C8",
      labelFontColor: "#7A7A7A"
    },
    axisY: {
      title: "Recette (DT)",
      valueFormatString: "#0.000",
      suffix: " DT",
      gridColor: "#F0E6D0",
      gridThickness: 1,
      labelFontSize: 11,
      labelFontColor: "#7A7A7A",
      titleFontColor: "#7A7A7A",
      titleFontSize: 12
    },
    toolTip: {
      borderColor: "#C8553D",
      borderThickness: 2,
      cornerRadius: 6,
      contentFormatter: function(e) {
        var date  = new Date(e.entries[0].dataPoint.x);
        var day   = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth()+1)).slice(-2);
        var year  = date.getFullYear();
        var val   = e.entries[0].dataPoint.y.toFixed(3);
        return "<strong>" + day + "/" + month + "/" + year + "</strong><br/>Recette : <strong>" + val + " DT</strong>";
      }
    },
    data: [{
      type: "spline",
      showInLegend: false,
      markerSize: 7,
      markerType: "circle",
      markerColor: "#C8553D",
      markerBorderColor: "#ffffff",
      markerBorderThickness: 2,
      color: "#C8553D",
      lineThickness: 3,
      xValueType: "dateTime",
      xValueFormatString: "DD/MM/YYYY",
      yValueFormatString: "#0.000 DT",
      dataPoints: dataPoints
    }]
  });

  chart.render();
})();
</script>
<?php endif; ?>