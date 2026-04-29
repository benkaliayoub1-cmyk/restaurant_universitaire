<?php
require_once __DIR__ . '/../../index.php';
$pageTitle='Historique'; $activeNav='historique';
$historique=(new MenuModel())->getHistorique(60);
include APP_ROOT.'/views/layouts/header.php';
?>
<section class="py-5">
  <div class="container">
    <div class="section-pill">Archive</div><h1 class="mb-4">Historique des menus</h1>
    <?php if(empty($historique)): ?>
      <div class="card border-0 shadow-sm text-center py-5"><div class="fs-1 mb-2">📋</div><h5>Aucun menu enregistré</h5></div>
    <?php else: ?>
      <div class="card border-0 shadow-sm overflow-hidden">
        <table class="table table-hover mb-0">
          <thead><tr><th>Date</th><th>Type</th><th>Plats</th><th>Prix</th><th>Réservations</th><th></th></tr></thead>
          <tbody>
            <?php foreach($historique as $m): ?>
              <tr>
                <td class="fw-semibold small"><?=date('d/m/Y',strtotime($m['dateMenu']))?></td>
                <td><span class="badge" style="background:rgba(200,85,61,.1);color:var(--c-primary)"><?=h($m['typeMenu'])?></span></td>
                <td class="small text-muted"><?=(int)$m['nb_repas']?> plats</td>
                <td class="fw-bold small text-primary"><?=formatPrix(MENU_PRIX_FIXE)?></td>
                <td><span class="badge bg-success"><?=(int)$m['nb_tickets']?></span></td>
                <td><a href="<?=APP_URL?>/views/public/menu.php?date=<?=$m['dateMenu']?>" class="btn btn-sm btn-outline-primary py-0">Voir</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
