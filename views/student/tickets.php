<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ETUDIANT);
$tm=new TicketModel(); $tm->expirerTickets();
$user=currentUser();
$tickets=$tm->getByEtudiant((int)$user['id']);
$stats=$tm->getStatsEtudiant((int)$user['id']);
$pageTitle='Mes tickets'; $activeNav='dashboard';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-collection me-2"></i>Mes Tickets</h2><p class="opacity-75 mb-0 small"><?=count($tickets)?> ticket(s)</p></div>
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-1">🎫</div><div><div class="kpi-label">Total</div><div class="kpi-value"><?=(int)$stats['total']?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-2">✅</div><div><div class="kpi-label">Utilisés</div><div class="kpi-value"><?=(int)$stats['utilises']?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-4">🔖</div><div><div class="kpi-label">Valides</div><div class="kpi-value"><?=(int)$stats['valides']?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-3">💰</div><div><div class="kpi-label">Dépensé</div><div class="kpi-value" style="font-size:1rem"><?=formatPrix($stats['depense'])?></div></div></div></div>
    </div>
    <?php if(empty($tickets)): ?>
      <div class="card border-0 shadow-sm text-center py-5"><div class="fs-1 mb-2">🎫</div><h5>Aucun ticket</h5><a href="<?=APP_URL?>/views/student/reservation.php" class="btn btn-primary mt-2">Réserver</a></div>
    <?php else: ?>
      <div class="card border-0 shadow-sm overflow-hidden">
        <table class="table table-hover mb-0">
          <thead><tr><th>#</th><th>Date repas</th><th>Type</th><th>Montant</th><th>Paiement</th><th>Statut</th><th></th></tr></thead>
          <tbody>
            <?php foreach($tickets as $t): ?>
              <tr>
                <td class="text-muted small">#<?=$t['id_ticket']?></td>
                <td class="fw-semibold small"><?=date('d/m/Y',strtotime($t['dateMenu']))?></td>
                <td><span class="badge" style="background:rgba(200,85,61,.1);color:var(--c-primary);font-size:.68rem"><?=h($t['typeMenu'])?></span></td>
                <td class="fw-bold small"><?=formatPrix((float)$t['montantTotal'])?></td>
                <td class="small text-muted"><?=$t['transactionD17']?'D17':'Espèces'?></td>
                <td><span class="sbadge s-<?=$t['status']?>"><span class="sbadge-dot"></span><?=$t['status']?></span></td>
                <td><a href="<?=APP_URL?>/views/student/ticket_view.php?id=<?=$t['id_ticket']?>" class="btn btn-sm btn-outline-primary py-0 px-2">Voir</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
