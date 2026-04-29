<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$um=new UserModel();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $ctrl=new UserController();
    $action=$_POST['action']??'';
    if ($action==='delete') $ctrl->deleteUser();
    elseif ($action==='edit') $ctrl->editUser();
}
$users=$um->getAll();
$pageTitle='Utilisateurs'; $activeNav='admin';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-people me-2"></i>Gestion des Utilisateurs</h2><p class="opacity-75 mb-0 small"><?=count($users)?> utilisateurs</p></div>
    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-header bg-white fw-bold small"><i class="bi bi-list-ul me-2 text-primary"></i>Liste</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Matricule</th><th>Actions</th></tr></thead>
          <tbody>
            <?php $roleColors=['admin'=>'warning','caissier'=>'info','etudiant'=>'primary'];
            foreach($users as $u): ?>
              <tr id="row-u-<?=$u['id']?>">
                <td class="small text-muted align-middle"><?=$u['id']?></td>
                <td class="align-middle">
                  <span class="view-mode fw-semibold small"><?=h($u['nom'])?></span>
                  <input type="text" class="form-control form-control-sm edit-mode d-none" value="<?=h($u['nom'])?>" style="max-width:150px">
                </td>
                <td class="align-middle">
                  <span class="view-mode small text-muted"><?=h($u['email'])?></span>
                  <input type="email" class="form-control form-control-sm edit-mode d-none" value="<?=h($u['email'])?>" style="max-width:190px">
                </td>
                <td class="align-middle"><span class="badge bg-<?=$roleColors[$u['role']]??'secondary'?> text-dark"><?=h($u['role'])?></span></td>
                <td class="small text-muted align-middle"><?=h($u['matricule']??'—')?></td>
                <td class="align-middle">
                  <div class="view-mode d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="editUser(<?=$u['id']?>)"><i class="bi bi-pencil"></i></button>
                    <?php if($u['id']!==(int)$_SESSION['user_id']): ?>
                      <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer <?=h($u['nom'])?> ?')">
                        <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?=$u['id']?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                      </form>
                    <?php endif; ?>
                  </div>
                  <form method="POST" class="edit-mode d-none d-flex gap-1" id="uf-<?=$u['id']?>">
                    <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?=$u['id']?>">
                    <input type="hidden" name="nom" class="un">
                    <input type="hidden" name="email" class="ue">
                    <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="saveUser(<?=$u['id']?>)"><i class="bi bi-check"></i></button>
                    <button type="button" class="btn btn-sm btn-secondary py-0 px-2" onclick="cancelUser(<?=$u['id']?>)"><i class="bi bi-x"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function editUser(id){const r=document.getElementById('row-u-'+id);r.querySelectorAll('.view-mode').forEach(e=>e.classList.add('d-none'));r.querySelectorAll('.edit-mode').forEach(e=>e.classList.remove('d-none'));}
function cancelUser(id){const r=document.getElementById('row-u-'+id);r.querySelectorAll('.view-mode').forEach(e=>e.classList.remove('d-none'));r.querySelectorAll('.edit-mode').forEach(e=>e.classList.add('d-none'));}
function saveUser(id){const r=document.getElementById('row-u-'+id);const f=document.getElementById('uf-'+id);f.querySelector('.un').value=r.querySelector('input[type="text"].edit-mode').value;f.querySelector('.ue').value=r.querySelector('input[type="email"].edit-mode').value;f.submit();}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
