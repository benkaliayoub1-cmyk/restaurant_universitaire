<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$mm=new MenuModel();
if ($_SERVER['REQUEST_METHOD']==='POST') { $ctrl=new MenuController(); $ctrl->gererRepas(); }
$page=max(1,(int)($_GET['page']??1));
$total=$mm->countRepas();
$pg=paginate($total,$page);
$repas=$mm->getAllRepas($pg['perPage'],$pg['offset']);
$pageTitle='Gérer les plats'; $activeNav='admin';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero"><h2 class="mb-1"><i class="bi bi-egg-fried me-2"></i>Gestion des Plats</h2><p class="opacity-75 mb-0 small"><?=$total?> plats enregistrés</p></div>
    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-plus-circle me-2 text-primary"></i>Ajouter un plat</div>
          <div class="card-body">
            <form method="POST" action="<?=APP_URL?>/views/admin/repas.php?page=<?=$pg['page']?>">
              <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
              <input type="hidden" name="action" value="add">
              <div class="mb-3"><label class="form-label fw-semibold small">Nom du plat</label><input type="text" name="nom" class="form-control" placeholder="Ex: Couscous agneau" required></div>
              <div class="mb-3"><label class="form-label fw-semibold small">Catégorie</label>
                <select name="categorie" class="form-select" required>
                  <option value="">-- Choisir --</option>
                  <option value="entree">🥗 Entrée</option>
                  <option value="plat">🍽️ Plat principal</option>
                  <option value="dessert">🍮 Dessert</option>
                  <option value="boisson">🥤 Boisson</option>
                  <option value="supplement">➕ Supplément</option>
                </select>
              </div>
              <div class="alert alert-info py-2 small mb-3"><i class="bi bi-info-circle me-1"></i>Prix affiché toujours <strong><?=formatPrix(MENU_PRIX_FIXE)?></strong> (fixe).</div>
              <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus me-1"></i>Ajouter</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-bold small"><i class="bi bi-list-ul me-2 text-primary"></i>Catalogue (<?=$total?> plats)</span>
            <span class="text-muted small">Page <?=$pg['page']?>/<?=$pg['totalPages']?></span>
          </div>
          <?php if(empty($repas)): ?>
            <div class="text-center text-muted py-5 small"><div class="fs-2 mb-2">🍽️</div>Aucun plat. Ajoutez-en un.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Nom</th><th>Catégorie</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php foreach($repas as $r): ?>
                    <tr id="row-<?=$r['id_repas']?>">
                      <td class="text-muted small align-middle"><?=$r['id_repas']?></td>
                      <td class="align-middle">
                        <span class="view-mode fw-semibold small"><?=h($r['nom'])?></span>
                        <input type="text" class="form-control form-control-sm edit-mode d-none" value="<?=h($r['nom'])?>" style="max-width:180px">
                      </td>
                      <td class="align-middle">
                        <span class="view-mode"><span class="cat-pill"><?=h($r['categorie'])?></span></span>
                        <select class="form-select form-select-sm edit-mode d-none" style="max-width:140px">
                          <?php foreach(['entree'=>'Entrée','plat'=>'Plat','dessert'=>'Dessert','boisson'=>'Boisson','supplement'=>'Supplément'] as $v=>$l): ?>
                            <option value="<?=$v?>" <?=$r['categorie']===$v?'selected':''?>><?=$l?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                      <td class="align-middle">
                        <div class="view-mode d-flex gap-1">
                          <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="editRow(<?=$r['id_repas']?>)"><i class="bi bi-pencil"></i></button>
                          <form method="POST" action="<?=APP_URL?>/views/admin/repas.php?page=<?=$pg['page']?>" class="d-inline" onsubmit="return confirm('Supprimer ce plat ?')">
                            <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?=$r['id_repas']?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                          </form>
                        </div>
                        <form method="POST" action="<?=APP_URL?>/views/admin/repas.php?page=<?=$pg['page']?>" class="edit-mode d-none d-flex gap-1" id="ef-<?=$r['id_repas']?>">
                          <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
                          <input type="hidden" name="action" value="edit">
                          <input type="hidden" name="id" value="<?=$r['id_repas']?>">
                          <input type="hidden" name="nom" class="en">
                          <input type="hidden" name="categorie" class="ec">
                          <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="saveRow(<?=$r['id_repas']?>)"><i class="bi bi-check"></i></button>
                          <button type="button" class="btn btn-sm btn-secondary py-0 px-2" onclick="cancelRow(<?=$r['id_repas']?>)"><i class="bi bi-x"></i></button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
        <?php if($pg['totalPages']>1): ?>
          <nav class="mt-3"><ul class="pagination pagination-sm justify-content-center mb-0">
            <li class="page-item <?=$pg['page']<=1?'disabled':''?>"><a class="page-link" href="?page=<?=$pg['page']-1?>">‹</a></li>
            <?php for($i=1;$i<=$pg['totalPages'];$i++): ?><li class="page-item <?=$i===$pg['page']?'active':''?>"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li><?php endfor; ?>
            <li class="page-item <?=$pg['page']>=$pg['totalPages']?'disabled':''?>"><a class="page-link" href="?page=<?=$pg['page']+1?>">›</a></li>
          </ul></nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
function editRow(id){const r=document.getElementById('row-'+id);r.querySelectorAll('.view-mode').forEach(e=>e.classList.add('d-none'));r.querySelectorAll('.edit-mode').forEach(e=>e.classList.remove('d-none'));}
function cancelRow(id){const r=document.getElementById('row-'+id);r.querySelectorAll('.view-mode').forEach(e=>e.classList.remove('d-none'));r.querySelectorAll('.edit-mode').forEach(e=>e.classList.add('d-none'));}
function saveRow(id){const r=document.getElementById('row-'+id);const f=document.getElementById('ef-'+id);f.querySelector('.en').value=r.querySelector('input[type="text"].edit-mode').value;f.querySelector('.ec').value=r.querySelector('select.edit-mode').value;f.submit();}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
