<?php
require_once __DIR__ . '/../../index.php';
if (isLoggedIn()) redirect('views/public/home.php');
if ($_SERVER['REQUEST_METHOD']==='POST') { $ctrl=new AuthController(); $ctrl->login(); }
$pageTitle='Connexion'; $activeNav='login';
$bodyClass='auth-bg d-flex flex-column';
$err=flash('error'); $suc=flash('success');
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
  <div class="auth-card" style="width:100%;max-width:430px">
    <div class="text-center mb-4">
      <h4 class="mb-1">Connexion</h4>
      <p class="text-muted small">Accédez à votre espace Resto ESEN</p>
    </div>
    <?php if($err): ?><div class="alert alert-danger d-flex align-items-center gap-2 py-2 small auto-dismiss"><i class="bi bi-exclamation-circle-fill"></i><?=h($err)?><button class="btn-close ms-auto" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if($suc): ?><div class="alert alert-success d-flex align-items-center gap-2 py-2 small auto-dismiss"><i class="bi bi-check-circle-fill"></i><?=h($suc)?><button class="btn-close ms-auto" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <form method="POST" action="<?=APP_URL?>/views/public/login.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
      <div class="mb-3">
        <label class="form-label fw-semibold small">Adresse email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" id="loginEmail" class="form-control" placeholder="votre@email.com" required autocomplete="email">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold small">Mot de passe</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" id="loginPwd" class="form-control" placeholder="••••••••" required autocomplete="current-password">
          <button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="loginPwd" title="Afficher/Masquer"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 btn-lg">Se connecter <i class="bi bi-arrow-right ms-1"></i></button>
    </form>
    <hr class="my-3">
    <p class="text-center small text-muted mb-3">Pas de compte ? <a href="<?=APP_URL?>/views/public/register.php" class="fw-bold">S'inscrire</a></p>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
