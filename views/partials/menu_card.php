<?php
// ============================================================
//  views/partials/menu_card.php — Carte menu réutilisable
//  Accepte $menuDuJour OU $menu (priorité à $menuDuJour)
//  IMPORTANT : Prix individuels des repas NON affichés
//  Seul le prix fixe total (MENU_PRIX_FIXE) est visible
// ============================================================
$_menuData = $menuDuJour ?? $menu ?? null;
if (!$_menuData) return;
$_catIcons = ['entree'=>'🥗','plat'=>'🍽️','dessert'=>'🍮','boisson'=>'🥤','supplement'=>'➕'];
?>
<div class="card border-0 shadow-sm overflow-hidden menu-card">
  <!-- En-tête dégradé terracotta -->
  <div class="menu-card-hdr">
    <span class="badge bg-white text-primary mb-2" style="font-size:.68rem;font-weight:700;letter-spacing:.06em">
      <?= strtoupper(h($_menuData['typeMenu'])) ?>
    </span>
    <h5 class="text-white mb-0">
      Menu du <?= date('d/m/Y', strtotime($_menuData['dateMenu'])) ?>
    </h5>
  </div>

  <!-- Liste des plats groupés par catégorie (SANS prix individuels) -->
  <?php if (!empty($_menuData['repas'])):
    $_grouped = [];
    foreach ($_menuData['repas'] as $_r) $_grouped[$_r['categorie']][] = $_r;
    foreach ($_grouped as $_cat => $_plats): ?>
      <div class="px-3 pt-2 pb-1">
        <div class="text-muted fw-bold text-uppercase mb-1"
             style="font-size:.66rem;letter-spacing:.07em">
          <?= ($_catIcons[$_cat] ?? '•') ?> <?= h($_cat) ?>
        </div>
        <?php foreach ($_plats as $_r): ?>
          <div class="py-1 small"><?= h($_r['nom']) ?></div>
        <?php endforeach; ?>
      </div>
  <?php endforeach; else: ?>
    <div class="px-3 py-2 text-muted small">Détails non disponibles.</div>
  <?php endif; ?>

  <!-- Prix FIXE uniquement — pas de prix par plat -->
  <div class="menu-total-row">
    <span class="fw-bold small">Prix du repas</span>
    <span class="menu-total-price"><?= formatPrix(MENU_PRIX_FIXE) ?></span>
  </div>
</div>
