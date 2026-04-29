<?php
// ============================================================
//  views/layout.php — Layout Bootstrap partagé
//  Chaque vue l'inclut en premier après require_once index.php
//  Variables : $pageTitle, $activeNav
// ============================================================
$user = currentUser();
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle ?? 'Resto ESEN') ?> — Resto ESEN</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍽️</text></svg>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="<?= h($bodyClass ?? '') ?>">

<nav class="navbar navbar-expand-lg navbar-resto sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand-resto" href="<?= APP_URL ?>/views/public/home.php">
      <div class="brand-icon">🍽️</div>Resto <span>ESEN</span>
    </a>
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='home'?'active':'' ?>" href="<?= APP_URL ?>/views/public/home.php"><i class="bi bi-house me-1"></i>Accueil</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='menu'?'active':'' ?>" href="<?= APP_URL ?>/views/public/menu.php"><i class="bi bi-journal-richtext me-1"></i>Menu</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='historique'?'active':'' ?>" href="<?= APP_URL ?>/views/public/historique.php"><i class="bi bi-clock-history me-1"></i>Historique</a></li>
        <?php if ($user): ?>
          <?php if ($role === ROLE_ETUDIANT): ?>
            <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='dashboard'?'active':'' ?>" href="<?= APP_URL ?>/views/student/dashboard.php"><i class="bi bi-grid me-1"></i>Mon espace</a></li>
            <li class="nav-item ms-lg-1"><a class="nav-link btn-nav-cta" href="<?= APP_URL ?>/views/student/reservation.php"><i class="bi bi-ticket me-1"></i>Réserver</a></li>
          <?php elseif ($role === ROLE_ADMIN): ?>
            <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='admin'?'active':'' ?>" href="<?= APP_URL ?>/views/admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Admin</a></li>
          <?php elseif ($role === ROLE_CAISSIER): ?>
            <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/views/cashier/scan.php"><i class="bi bi-qr-code-scan me-1"></i>Scanner</a></li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= APP_URL ?>/views/public/logout.php" style="color:rgba(255,255,255,.45)!important">
              <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= ($activeNav??'')==='login'?'active':'' ?>" href="<?= APP_URL ?>/views/public/login.php"><i class="bi bi-person me-1"></i>Connexion</a></li>
          <li class="nav-item ms-lg-1"><a class="nav-link btn-nav-cta" href="<?= APP_URL ?>/views/public/register.php"><i class="bi bi-person-plus me-1"></i>S'inscrire</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
