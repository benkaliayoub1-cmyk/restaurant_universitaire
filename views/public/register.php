<?php
require_once __DIR__ . '/../../index.php';
if (isLoggedIn()) redirect('views/public/home.php');
if ($_SERVER['REQUEST_METHOD']==='POST') { $ctrl=new AuthController(); $ctrl->register(); }
$pageTitle='Inscription'; $bodyClass='auth-bg d-flex flex-column';
$err=flash('error');
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
  <div class="auth-card" style="width:100%;max-width:490px">
    <div class="text-center mb-4">
      <h4 class="mb-1">Créer un compte</h4>
      <p class="text-muted small">Rejoignez le Resto ESEN en tant qu'étudiant</p>
    </div>
    <?php if($err): ?><div class="alert alert-danger d-flex align-items-center gap-2 py-2 small auto-dismiss"><i class="bi bi-exclamation-circle-fill"></i><?=h($err)?><button class="btn-close ms-auto" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <form method="POST" action="<?=APP_URL?>/views/public/register.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
      <div class="row g-3 mb-3">
        <div class="col-md-7">
          <label class="form-label fw-semibold small">Nom complet</label>
          <div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" name="nom" class="form-control" placeholder="Prénom Nom" required></div>
        </div>
        <div class="col-md-5">
          <label class="form-label fw-semibold small">Matricule</label>
          <div class="input-group"><span class="input-group-text"><i class="bi bi-card-text"></i></span><input type="text" name="matricule" class="form-control" placeholder="12345678" required></div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold small">Email</label>
        <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" placeholder="votre@email.com" required></div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="regPwd" class="form-control" placeholder="Min. 6 car." required minlength="6">
            <button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="regPwd"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Confirmer</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="confirm_password" id="regPwdC" class="form-control" placeholder="Répétez" required>
            <button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="regPwdC"><i class="bi bi-eye"></i></button>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 btn-lg">Créer mon compte <i class="bi bi-arrow-right ms-1"></i></button>
    </form>
    <hr class="my-3">
    <p class="text-center small text-muted">Déjà inscrit ? <a href="<?=APP_URL?>/views/public/login.php" class="fw-bold">Se connecter</a></p>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
