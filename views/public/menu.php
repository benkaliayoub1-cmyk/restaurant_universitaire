<?php
require_once __DIR__ . '/../../index.php';
$pageTitle='Menu du jour'; $activeNav='menu';
$mm=new MenuModel(); $tm=new TicketModel();
$tm->expirerTickets();
$date = (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date']))
      ? $_GET['date'] : date('Y-m-d');
$menuDuJour = $mm->getMenuByDate($date);
$nbTickets  = $tm->countTicketsDuJour();
$attente    = estimerAttente($nbTickets);
$isToday    = ($date === date('Y-m-d'));
include APP_ROOT.'/views/layouts/header.php';
?>
<section class="py-5">
  <div class="container" style="max-width:740px">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
      <div>
        <div class="section-pill">Menu</div>
        <h1 class="mb-0">
          <?= $isToday ? 'Menu du jour' : 'Menu du '.date('d/m/Y',strtotime($date)) ?>
        </h1>
      </div>
      <form method="GET" class="d-flex gap-2 align-items-end">
        <div>
          <label class="form-label small fw-semibold mb-1">Choisir une date</label>
          <input type="date" name="date" class="form-control form-control-sm"
                 value="<?= $date ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
      </form>
    </div>

    <?php if ($menuDuJour): ?>
      <?php include APP_ROOT.'/views/partials/menu_card.php'; ?>

      <?php if ($isToday): ?>
        <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">
          <div class="wait-pill"><span class="wait-dot"></span>Attente : <strong><?= $attente ?></strong></div>
          <span class="text-muted small"><?= $nbTickets ?> réservation(s)</span>
        </div>
        <div class="mt-3 d-flex gap-2">
          <?php if (isLoggedIn() && hasRole(ROLE_ETUDIANT)): ?>
            <a href="<?= APP_URL ?>/views/student/reservation.php" class="btn btn-primary">
              <i class="bi bi-ticket me-1"></i>Réserver (<?= formatPrix(MENU_PRIX_FIXE) ?>)
            </a>
          <?php elseif (!isLoggedIn()): ?>
            <a href="<?= APP_URL ?>/views/public/login.php" class="btn btn-primary">Se connecter</a>
            <a href="<?= APP_URL ?>/views/public/register.php" class="btn btn-outline-primary">S'inscrire</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="fs-1 mb-2">📋</div>
        <h5><?= $isToday
              ? "Le menu du jour n'a pas encore été publié."
              : "Aucun menu pour cette date." ?>
        </h5>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
