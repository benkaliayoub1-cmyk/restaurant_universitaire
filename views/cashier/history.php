<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_CAISSIER);
$tm=new TicketModel();
$validations=$tm->getValidationsDuJour((int)$_SESSION['user_id']);
$totalMontant=array_sum(array_column($validations,'montantTotal'));
$pageTitle='Validations du jour'; $activeNav='';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-list-check me-2"></i>Validations du Jour</h2><p class="opacity-75 mb-0 small"><?=date('d/m/Y')?> — <?=count($validations)?> validation(s)</p></div>
    <?php if(empty($validations)): ?>
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="fs-1 mb-2 opacity-25">📋</div>
        <h5 class="text-muted">Aucune validation aujourd'hui</h5>
        <a href="<?=APP_URL?>/views/cashier/scan.php" class="btn btn-primary mt-2 mx-auto" style="width:fit-content"><i class="bi bi-qr-code-scan me-1"></i>Scanner un ticket</a>
      </div>
    <?php else: ?>
      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-bold small"><i class="bi bi-check-all me-2 text-success"></i>Tickets validés — <?=date('d/m/Y')?></span>
          <span class="fw-bold small">Total : <span class="text-primary"><?=formatPrix($totalMontant)?></span></span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Étudiant</th><th>Matricule</th><th>Menu</th><th>Montant</th><th>Heure</th></tr></thead>
            <tbody>
              <?php foreach($validations as $v): ?>
                <tr>
                  <td class="text-muted small">#<?=$v['id_ticket']?></td>
                  <td class="fw-semibold small"><?=h($v['etudiant_nom'])?></td>
                  <td class="small text-muted"><?=h($v['matricule'])?></td>
                  <td><span class="cat-pill"><?=h($v['typeMenu'])?></span></td>
                  <td class="fw-bold small text-success"><?=formatPrix((float)$v['montantTotal'])?></td>
                  <td class="small"><?=date('H:i:s',strtotime($v['dateValidation']))?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
