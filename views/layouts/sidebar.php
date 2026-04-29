<?php
// ============================================================
//  views/sidebar.php — Sidebar dynamique unique (tous les rôles)
// ============================================================
$_u    = currentUser();
$_role = $_SESSION['role'] ?? '';
$_p    = basename($_SERVER['PHP_SELF']);

$_menus = [
    ROLE_ETUDIANT => [
        'color' => '#C8553D', 'label' => 'Étudiant',
        'sections' => [
            ['titre' => 'Principal', 'liens' => [
                ['icon'=>'bi-grid',        'nom'=>'Tableau de bord',  'file'=>'dashboard.php',   'url'=>'views/student/dashboard.php'],
                ['icon'=>'bi-ticket',      'nom'=>'Réserver',         'file'=>'reservation.php', 'url'=>'views/student/reservation.php'],
                ['icon'=>'bi-collection',  'nom'=>'Mes tickets',      'file'=>'tickets.php',     'url'=>'views/student/tickets.php'],
            ]],
            ['titre' => 'Compte', 'liens' => [
                ['icon'=>'bi-person',           'nom'=>'Mon profil',   'file'=>'profile.php', 'url'=>'views/student/profile.php'],
                ['icon'=>'bi-journal-richtext',  'nom'=>'Menu du jour','file'=>'menu.php',    'url'=>'views/public/menu.php'],
                ['icon'=>'bi-box-arrow-right',   'nom'=>'Déconnexion', 'file'=>'logout.php',  'url'=>'views/public/logout.php'],
            ]],
        ],
    ],
    ROLE_ADMIN => [
        'color' => '#D4A853', 'label' => 'Administrateur',
        'sections' => [
            ['titre' => 'Gestion', 'liens' => [
                ['icon'=>'bi-speedometer2',    'nom'=>'Tableau de bord', 'file'=>'dashboard.php', 'url'=>'views/admin/dashboard.php'],
                ['icon'=>'bi-journal-plus',    'nom'=>'Saisir le menu',  'file'=>'menu.php',      'url'=>'views/admin/menu.php'],
                ['icon'=>'bi-egg-fried',       'nom'=>'Gérer les plats', 'file'=>'repas.php',     'url'=>'views/admin/repas.php'],
                ['icon'=>'bi-ticket-detailed', 'nom'=>'Tickets vendus',  'file'=>'tickets.php',   'url'=>'views/admin/tickets.php'],
                ['icon'=>'bi-people',          'nom'=>'Utilisateurs',    'file'=>'users.php',     'url'=>'views/admin/users.php'],
            ]],
            ['titre' => 'Compte', 'liens' => [
                ['icon'=>'bi-person',          'nom'=>'Mon profil',  'file'=>'profile.php', 'url'=>'views/admin/profile.php'],
                ['icon'=>'bi-box-arrow-right', 'nom'=>'Déconnexion', 'file'=>'logout.php',  'url'=>'views/public/logout.php'],
            ]],
        ],
    ],
    ROLE_CAISSIER => [
        'color' => '#3A5A40', 'label' => 'Caissier',
        'sections' => [
            ['titre' => 'Actions', 'liens' => [
                ['icon'=>'bi-qr-code-scan', 'nom'=>'Scanner un ticket',   'file'=>'scan.php',    'url'=>'views/cashier/scan.php'],
                ['icon'=>'bi-list-check',   'nom'=>'Validations du jour',  'file'=>'history.php', 'url'=>'views/cashier/history.php'],
            ]],
            ['titre' => 'Compte', 'liens' => [
                ['icon'=>'bi-box-arrow-right','nom'=>'Déconnexion','file'=>'logout.php','url'=>'views/public/logout.php'],
            ]],
        ],
    ],
];

$_cfg = $_menus[$_role] ?? null;
if (!$_cfg) return;
$_initial = strtoupper(mb_substr($_u['nom'] ?? 'U', 0, 1));
?>
<aside class="sidebar-wrap d-none d-lg-flex flex-column">
  <div class="sidebar-user">
    <div class="d-flex align-items-center gap-2">
      <div class="s-avatar" style="background:<?= $_cfg['color'] ?>"><?= $_initial ?></div>
      <div>
        <div class="s-name"><?= h($_u['nom'] ?? '') ?></div>
        <div class="s-role"><?= $_cfg['label'] ?>
          <?php if (!empty($_u['matricule'])): ?><br><small><?= h($_u['matricule']) ?></small><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <nav class="flex-grow-1 overflow-y-auto">
    <?php foreach ($_cfg['sections'] as $sec): ?>
      <div class="s-section"><?= $sec['titre'] ?></div>
      <?php foreach ($sec['liens'] as $lien): ?>
        <a href="<?= APP_URL ?>/<?= $lien['url'] ?>"
           class="s-link <?= $_p === $lien['file'] ? 'active' : '' ?>">
          <i class="bi <?= $lien['icon'] ?> si"></i> <?= $lien['nom'] ?>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </nav>
</aside>
