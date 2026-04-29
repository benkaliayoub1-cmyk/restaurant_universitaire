<?php
require_once __DIR__ . '/../../index.php';
$pageTitle='Accueil'; $activeNav='home';
$mm=new MenuModel(); $tm=new TicketModel();
$tm->expirerTickets();
$menuDuJour=$mm->getMenuDuJour();
$nbTickets=$tm->countTicketsDuJour();
$attente=estimerAttente($nbTickets);
$historique=$mm->getHistorique(5);
// ============================================================
// IMAGES CARROUSEL — Remplacez les URLs par vos propres photos
// ============================================================
$slides=[
  ['img'=>APP_URL.'/public/assets/palt carroussel.jpg','title'=>'Bienvenue au Resto ESEN','desc'=>'Votre restaurant universitaire numérique.','cta_label'=>'Voir le menu','cta_url'=>APP_URL.'/views/public/menu.php'],
  ['img'=>APP_URL.'/public/assets/carroussel1.jpg','title'=>'Plats tunisiens chaque jour','desc'=>'Couscous, tajine, brik — servis avec soin.','cta_label'=>'S\'inscrire','cta_url'=>APP_URL.'/views/public/register.php'],
  ['img'=>APP_URL.'/public/assets/d17.png','title'=>'Ticket numérique & QR Code','desc'=>'Payez D17 ou espèces — ticket instantané.','cta_label'=>'Créer un compte','cta_url'=>APP_URL.'/views/public/register.php'],
  ];
// ============================================================
// IMAGES GALERIE — Remplacez les URLs par vos propres photos
// ============================================================
$gallery=[
  ['url'=>APP_URL.'/public/assets/plat.jpg','cap'=>' yaourt'],
  ['url'=>APP_URL.'/public/assets/plat1.jpg','cap'=>'Cuisine fraîche'],
  ['url'=>APP_URL.'/public/assets/plat2.jpg','cap'=>'Espace convivial'],
  ['url'=>APP_URL.'/public/assets/plat3.jpg','cap'=>'Service rapide'],
  ['url'=>APP_URL.'/public/assets/plat4.jpg','cap'=>'Menu du jour'],
  ['url'=>APP_URL.'/public/assets/plat5.jpg','cap'=>'escalopes panées'],
  ['url'=>APP_URL.'/public/assets/plat6.jpg','cap'=>'lasagnes'],
  ['url'=>APP_URL.'/public/assets/plat7.jpg','cap'=>'salade'],
  ['url'=>APP_URL.'/public/assets/plat8.jpg','cap'=>'loubiaa'],
  ['url'=>APP_URL.'/public/assets/plat9.jpg','cap'=>'Réservations faciles']
];
include APP_ROOT.'/views/layouts/header.php';
?>
<!-- CAROUSEL -->
<section class="hero-carousel">
  <?php foreach($slides as $i=>$s): ?>
    <div class="carousel-slide <?=$i===0?'active':''?>" style="background-image:url('<?=$s['img']?>')">
      <div class="carousel-content">
        <h1><?=$s['title']?></h1><p><?=$s['desc']?></p>
        <a href="<?=$s['cta_url']?>" class="btn btn-primary btn-lg px-4"><?=$s['cta_label']?> <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
    </div>
  <?php endforeach; ?>
  <div class="c-dots"><?php foreach($slides as $i=>$_): ?><button class="c-dot <?=$i===0?'active':''?>"></button><?php endforeach; ?></div>
</section>
<!-- MENU + STATS -->
<section class="py-5">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-lg-8">
        <div class="section-pill">Aujourd'hui · <?=date('d/m/Y')?></div>
        <h2 class="mb-4">Menu du Jour</h2>
        <?php if($menuDuJour): ?>
          <?php include APP_ROOT.'/views/partials/menu_card.php'; ?>
          <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">
            <div class="wait-pill"><span class="wait-dot"></span>Attente estimée : <strong><?=$attente?></strong></div>
            <span class="text-muted small"><?=$nbTickets?> réservation(s)</span>
          </div>
          <div class="mt-3 d-flex gap-2">
            <?php if(isLoggedIn()&&hasRole(ROLE_ETUDIANT)): ?>
              <a href="<?=APP_URL?>/views/student/reservation.php" class="btn btn-primary"><i class="bi bi-ticket me-1"></i>Réserver (<?=formatPrix(MENU_PRIX_FIXE)?>)</a>
            <?php elseif(!isLoggedIn()): ?>
              <a href="<?=APP_URL?>/views/public/register.php" class="btn btn-primary">Créer un compte</a>
              <a href="<?=APP_URL?>/views/public/login.php" class="btn btn-outline-primary">Se connecter</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="card border-0 shadow-sm text-center py-5"><div class="fs-1 mb-2">🍽️</div><h5>Pas encore de menu aujourd'hui</h5></div>
        <?php endif; ?>
      </div>
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-bar-chart me-2 text-primary"></i>En chiffres</div>
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center rounded p-2 mb-2" style="background:var(--c-cream)"><span class="small">🎫 Réservations aujourd'hui</span><strong class="text-primary"><?=$nbTickets?></strong></div>
            <div class="d-flex justify-content-between align-items-center rounded p-2 mb-2" style="background:var(--c-cream)"><span class="small">⏱️ Temps d'attente</span><strong style="color:var(--c-accent)"><?=$attente?></strong></div>
            <div class="d-flex justify-content-between align-items-center rounded p-2" style="background:var(--c-cream)"><span class="small">💰 Prix du menu</span><strong class="text-success"><?=formatPrix(MENU_PRIX_FIXE)?></strong></div>
          </div>
        </div>
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold small"><i class="bi bi-clock-history me-2 text-primary"></i>Menus récents</div>
          <?php foreach($historique as $m): ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--c-border)">
              <div><div class="fw-semibold small"><?=date('d/m/Y',strtotime($m['dateMenu']))?></div><div class="text-muted" style="font-size:.72rem"><?=$m['nb_tickets']?> tickets</div></div>
              <span class="fw-bold small text-primary"><?=formatPrix(MENU_PRIX_FIXE)?></span>
            </div>
          <?php endforeach; ?>
          <div class="card-footer bg-white"><a href="<?=APP_URL?>/views/public/historique.php" class="btn btn-sm btn-outline-primary w-100">Voir tout →</a></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- GALERIE -->
<section class="py-5" style="background:#fff">
  <div class="container">
    <div class="section-pill">Notre restaurant</div><h2 class="mb-4">Galerie</h2>
    <div class="gallery-grid">
      <?php foreach($gallery as $g): ?><div class="gallery-item"><img src="<?=$g['url']?>" alt="<?=h($g['cap'])?>" loading="lazy"><div class="gallery-overlay"><?=h($g['cap'])?></div></div><?php endforeach; ?>
    </div>
  </div>
</section>
<?php if(!isLoggedIn()): ?>
<section class="py-5" style="background:linear-gradient(135deg,var(--c-primary),var(--c-primary-d))">
  <div class="container text-center text-white">
    <h2 class="mb-2">Rejoignez le Resto ESEN</h2>
    <p class="opacity-75 mb-4">Créez votre compte et réservez pour <strong><?=formatPrix(MENU_PRIX_FIXE)?></strong> seulement.</p>
    <a href="<?=APP_URL?>/views/public/register.php" class="btn btn-warning btn-lg px-5 fw-bold">Créer mon compte <i class="bi bi-arrow-right ms-1"></i></a>
  </div>
</section>
<?php endif; ?>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
