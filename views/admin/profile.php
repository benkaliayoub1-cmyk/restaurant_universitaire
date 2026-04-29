<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ctrl=new UserController();
    if (($_POST['action']??'')==='profile') $ctrl->updateProfile(ROLE_ADMIN);
    else $ctrl->updatePassword(ROLE_ADMIN);
}
$user=currentUser();
$pageTitle='Mon profil Admin'; $activeNav='admin';
include APP_ROOT.'/views/layout.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-person-circle me-2"></i>Mon Profil Admin</h2><p class="opacity-75 mb-0 small"><?=h($user['email']??'')?></p></div>
    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-pencil me-2 text-primary"></i>Informations</div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
              <input type="hidden" name="action" value="profile">
              <div class="mb-3"><label class="form-label fw-semibold small">Nom</label><input type="text" name="nom" class="form-control" value="<?=h($user['nom']??'')?>" required></div>
              <div class="mb-3"><label class="form-label fw-semibold small">Email</label><input type="email" name="email" class="form-control" value="<?=h($user['email']??'')?>" required></div>
              <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-lock me-2 text-primary"></i>Mot de passe</div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
              <input type="hidden" name="action" value="password">
              <div class="mb-3"><label class="form-label fw-semibold small">Ancien</label>
                <div class="input-group"><input type="password" name="old_password" id="ap1" class="form-control" required><button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="ap1"><i class="bi bi-eye"></i></button></div></div>
              <div class="mb-3"><label class="form-label fw-semibold small">Nouveau</label>
                <div class="input-group"><input type="password" name="new_password" id="ap2" class="form-control" minlength="6" required><button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="ap2"><i class="bi bi-eye"></i></button></div></div>
              <div class="mb-3"><label class="form-label fw-semibold small">Confirmer</label>
                <div class="input-group"><input type="password" name="confirm_password" id="ap3" class="form-control" required><button type="button" class="btn btn-outline-secondary pwd-toggle" data-target="ap3"><i class="bi bi-eye"></i></button></div></div>
              <button type="submit" class="btn btn-outline-primary w-100">Modifier</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include APP_ROOT.'/views/footer.php'; ?>
