<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ETUDIANT);
$id=(int)($_GET['id']??0);
$tm=new TicketModel();
$ticket=$tm->getById($id);
if (!$ticket||(int)$ticket['id_etudiant']!==(int)$_SESSION['user_id']) redirect('views/student/dashboard.php');
$pageTitle='Ticket #'.$id; $activeNav='dashboard';
$suc=flash('success');
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div><h2 class="mb-1">Ticket #<?=$id?></h2><p class="text-muted small mb-0">Réservé le <?=date('d/m/Y à H:i',strtotime($ticket['dateAchat']))?></p></div>
      <span class="sbadge s-<?=$ticket['status']?>" style="font-size:.8rem;padding:.3rem 1rem"><span class="sbadge-dot"></span><?=strtoupper($ticket['status'])?></span>
    </div>
    <?php if($suc): ?><div class="alert alert-success py-2 small mb-3 auto-dismiss d-flex gap-2"><i class="bi bi-check-circle-fill"></i><?=h($suc)?><button class="btn-close ms-auto" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if($ticket['status']==='annule'): ?><div class="alert alert-danger small mb-3"><i class="bi bi-x-circle me-1"></i>Ce ticket a expiré ou a été annulé.</div><?php endif; ?>
    <?php if($ticket['status']==='utilise'): ?><div class="alert alert-info small mb-3"><i class="bi bi-check-circle me-1"></i>Ticket déjà utilisé. Bon appétit !</div><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-5">
        <div class="card border-0 shadow-sm text-center p-4">
          <h6 class="fw-bold mb-1">Votre QR Code</h6>
          <p class="text-muted small mb-3">Présentez ce code à l'entrée</p>
          <?php if($ticket['status']==='valide'): ?>
            <div class="qr-wrap mb-2 mx-auto d-inline-block">
              <canvas id="qr-canvas" data-qr="<?=h($ticket['qrCode'])?>" width="180" height="180"></canvas>
            </div>
            <div class="qr-str mt-2"><?=h($ticket['qrCode'])?></div>
            <?php if($ticket['dateMenu']===date('Y-m-d')): ?><div class="alert alert-warning py-1 small mt-2 mb-0"><i class="bi bi-clock me-1"></i>Expire ce soir à minuit</div><?php endif; ?>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm mt-3 w-100"><i class="bi bi-printer me-1"></i>Imprimer</button>
          <?php else: ?>
            <div class="d-flex align-items-center justify-content-center mx-auto rounded" style="width:180px;height:180px;background:var(--c-cream);font-size:3rem;opacity:.5">🚫</div>
            <p class="text-muted small mt-2">QR non disponible</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-info-circle me-2 text-primary"></i>Détails</div>
          <div class="card-body p-0">
            <?php $rows=[
              ['bi-hash','Numéro','#'.$ticket['id_ticket']],
              ['bi-calendar','Date repas',date('d/m/Y',strtotime($ticket['dateMenu']))],
              ['bi-journal','Type',ucfirst(h($ticket['typeMenu']))],
              ['bi-person','Étudiant',h($ticket['etudiant_nom'])],
              ['bi-card-text','Matricule',h($ticket['matricule'])],
              ['bi-envelope','Email',h($ticket['etudiant_email'])],
              ['bi-cash','Montant',formatPrix((float)$ticket['montantTotal'])],
              ['bi-credit-card','Paiement',$ticket['transactionD17']?'D17 ('.h($ticket['transactionD17']).')':'Espèces'],
            ]; ?>
            <?php foreach($rows as [$ico,$lbl,$val]): ?>
              <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--c-border)">
                <span class="text-muted small"><i class="bi <?=$ico?> me-2"></i><?=$lbl?></span>
                <span class="fw-semibold small"><?=$val?></span>
              </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-2">
              <span class="text-muted small"><i class="bi bi-toggle-on me-2"></i>Statut</span>
              <span class="sbadge s-<?=$ticket['status']?>"><span class="sbadge-dot"></span><?=$ticket['status']?></span>
            </div>
          </div>
        </div>
        <a href="<?=APP_URL?>/views/student/tickets.php" class="btn btn-outline-primary w-100 mt-3"><i class="bi bi-arrow-left me-1"></i>Mes tickets</a>
      </div>
    </div>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
