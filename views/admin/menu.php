<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$mm = new MenuModel();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new MenuController();
    $ctrl->creerMenu();
}
$repas      = $mm->getAllRepas(200, 0);
$historique = $mm->getHistorique(10);
$menuDuJour = $mm->getMenuDuJour();  // pour vérifier si menu existe déjà
$pageTitle  = 'Saisir le menu'; $activeNav = 'admin';
include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">
    <div class="page-hero">
      <h2 class="mb-1"><i class="bi bi-journal-plus me-2"></i>Saisir le Menu du Jour</h2>
      <p class="opacity-75 mb-0 small">Prix fixe : <strong><?= formatPrix(MENU_PRIX_FIXE) ?></strong> pour tous les menus</p>
    </div>

    <?php include APP_ROOT.'/views/partials/alerts.php'; ?>

    <?php if ($menuDuJour): ?>
      <div class="alert alert-info mb-3">
        <i class="bi bi-check-circle me-2"></i>
        Un menu a déjà été publié pour aujourd'hui (<?= date('d/m/Y') ?>).
        Vous pouvez en créer un pour une autre date.
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small">
            <i class="bi bi-calendar-plus me-2 text-primary"></i>Nouveau menu
          </div>
          <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/views/admin/menu.php">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Date du menu</label>
                  <input type="date" name="date_menu" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Type de repas</label>
                  <select name="type_menu" class="form-select">
                    <option value="petit-dejeuner">🌅 Petit-déjeuner</option>
                    <option value="dejeuner" selected>☀️ Déjeuner</option>
                    <option value="diner">🌙 Dîner</option>
                  </select>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold small mb-0">
                  Sélectionner les plats <span class="text-danger">*</span>
                </label>
                <span class="text-muted small">Prix affiché : <strong class="text-primary"><?= formatPrix(MENU_PRIX_FIXE) ?></strong></span>
              </div>

              <?php if (empty($repas)): ?>
                <div class="alert alert-warning small py-2">
                  Aucun plat disponible.
                  <a href="<?= APP_URL ?>/views/admin/repas.php">Ajoutez des plats d'abord →</a>
                </div>
              <?php else:
                $cats = [];
                foreach ($repas as $r) $cats[$r['categorie']][] = $r;
                $icons = ['entree'=>'🥗','plat'=>'🍽️','dessert'=>'🍮','boisson'=>'🥤','supplement'=>'➕'];
                foreach ($cats as $cat => $plats): ?>
                  <p class="text-muted fw-bold text-uppercase mt-3 mb-2 small" style="letter-spacing:.07em">
                    <?= ($icons[$cat] ?? '•') ?> <?= h($cat) ?>
                  </p>
                  <div class="row g-2">
                    <?php foreach ($plats as $r): ?>
                      <div class="col-md-6">
                        <label class="d-flex align-items-center gap-2 p-2 rounded border small"
                               style="cursor:pointer;transition:.15s"
                               onmouseover="this.style.background='var(--c-cream)'"
                               onmouseout="this.style.background=''">
                          <input class="form-check-input m-0 flex-shrink-0"
                                 type="checkbox" name="repas_ids[]" value="<?= $r['id_repas'] ?>">
                          <span><?= h($r['nom']) ?></span>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>
              <?php endforeach; endif; ?>

              <button type="submit" class="btn btn-primary w-100 mt-4">
                <i class="bi bi-send me-1"></i>Publier le menu
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small">
            <i class="bi bi-clock-history me-2 text-primary"></i>Menus récents
          </div>
          <div class="card-body p-0">
            <?php foreach ($historique as $m): ?>
              <div class="d-flex justify-content-between align-items-center px-3 py-2"
                   style="border-bottom:1px solid var(--c-border)">
                <div>
                  <div class="fw-semibold small"><?= date('d/m/Y', strtotime($m['dateMenu'])) ?></div>
                  <div class="text-muted" style="font-size:.72rem"><?= h($m['typeMenu']) ?> · <?= $m['nb_repas'] ?> plats</div>
                </div>
                <div class="text-end">
                  <div class="fw-bold small text-primary"><?= formatPrix(MENU_PRIX_FIXE) ?></div>
                  <div class="text-muted" style="font-size:.71rem"><?= $m['nb_tickets'] ?> tickets</div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($historique)): ?>
              <div class="text-center text-muted py-3 small">Aucun menu</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
