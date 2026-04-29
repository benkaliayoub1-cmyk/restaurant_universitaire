<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_CAISSIER);
$tm=new TicketModel(); $tm->expirerTickets();
if ($_SERVER['REQUEST_METHOD']==='POST') { $ctrl=new TicketController(); $ctrl->scanner(); }
$scanResult=$_SESSION['scan_result']??null;
unset($_SESSION['scan_result']);
$pageTitle='Scanner un ticket'; $activeNav='';
include APP_ROOT.'/views/layout.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-qr-code-scan me-2"></i>Scanner un Ticket</h2><p class="opacity-75 mb-0 small"><?=date('d/m/Y')?> — Valider l'accès au restaurant</p></div>
    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>
    <div class="row g-4 justify-content-center">
      <div class="col-lg-5">
        <div class="scan-zone mb-4">
          <div class="scan-ring">📷</div>
          <h5 class="mb-1">Scanner le QR Code</h5>
          <p class="small opacity-60 mb-0">Utilisez un lecteur USB ou saisissez manuellement</p>
        </div>
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-keyboard me-2 text-primary"></i>Saisir le code</div>
          <div class="card-body">
            <form method="POST" id="scan-form" action="<?=APP_URL?>/views/cashier/scan.php">
              <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
              <div class="mb-3">
                <label class="form-label fw-semibold small">Code QR du ticket</label>
                <input type="text" name="qr_code" id="qr-input" class="form-control font-monospace"
                       placeholder="Ex: A3F2B1C0-4-1234567890"
                       autofocus autocomplete="off" required>
                <div class="form-text small">Collez le code ou scannez via lecteur USB (Entrée = valider).</div>
              </div>
              <button type="submit" class="btn btn-primary w-100 btn-lg"><i class="bi bi-check-circle me-1"></i>Valider le ticket</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <?php if($scanResult): ?>
          <div class="<?=$scanResult['ok']?'scan-ok':'scan-fail'?> mb-3">
            <div class="fs-1 mb-2"><?=$scanResult['ok']?'✅':'❌'?></div>
            <h4 class="fw-bold mb-2"><?=$scanResult['ok']?'Accès Autorisé':'Accès Refusé'?></h4>
            <p class="mb-0"><?=h($scanResult['msg'])?></p>
            <?php if($scanResult['ok']&&isset($scanResult['ticket'])): $t=$scanResult['ticket']; ?>
              <hr style="border-color:currentColor;opacity:.2">
              <div class="text-start small">
                <div class="d-flex justify-content-between py-1"><span>Étudiant</span><strong><?=h($t['etudiant_nom'])?></strong></div>
                <div class="d-flex justify-content-between py-1"><span>Matricule</span><strong><?=h($t['matricule']??'—')?></strong></div>
                <div class="d-flex justify-content-between py-1"><span>Menu</span><strong><?=h($t['typeMenu'])?> · <?=date('d/m/Y',strtotime($t['dateMenu']))?></strong></div>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="card border-0 shadow-sm text-center py-5">
            <div class="fs-1 mb-2 opacity-25">🎫</div>
            <h6 class="text-muted">En attente d'un scan</h6>
            <p class="text-muted small mb-0">Le résultat apparaîtra ici.</p>
          </div>
        <?php endif; ?>
        <div class="card border-0 shadow-sm mt-3">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-info-circle me-2 text-primary"></i>Conditions de validité</div>
          <div class="card-body p-3 small text-muted">
            <p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Statut <strong>valide</strong></p>
            <p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Date = <strong>aujourd'hui (<?=date('d/m/Y')?>)</strong></p>
            <p class="mb-1"><i class="bi bi-x-circle-fill text-danger me-2"></i>Ticket déjà <strong>utilisé</strong> → refusé</p>
            <p class="mb-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Ticket <strong>expiré</strong> → refusé</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('qr-input').addEventListener('keypress', e => {
  if (e.key === 'Enter') { e.preventDefault(); document.getElementById('scan-form').submit(); }
});
</script>
<?php include APP_ROOT.'/views/footer.php'; ?>
