<?php
require_once __DIR__ . '/../../index.php';
requireAuth(ROLE_ADMIN);
$tm = new TicketModel(); $tm->expirerTickets();
$stats = $tm->getStatsAdmin();
$pageTitle = 'Tickets vendus'; $activeNav = 'admin';

// ── Pagination ───────────────────────────────────────────────
$parPage    = 15;
$totalItems = $tm->countAll();
$totalPages = (int)ceil($totalItems / $parPage);
$pageCourante = max(1, min((int)($_GET['page'] ?? 1), $totalPages ?: 1));
$offset     = ($pageCourante - 1) * $parPage;
$tickets    = $tm->getAllPagine($parPage, $offset);

include APP_ROOT.'/views/layouts/header.php';
?>
<div class="d-flex flex-grow-1" style="min-height:calc(100vh - 68px)">
  <?php include APP_ROOT.'/views/layouts/sidebar.php'; ?>
  <div class="dashboard-main w-100">

    <!-- Hero -->
    <div class="page-hero">
      <h2 class="mb-1"><i class="bi bi-ticket-detailed me-2"></i>Tickets Vendus</h2>
      <p class="opacity-75 mb-0 small"><?= $totalItems ?> tickets au total</p>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-1">🎫</div><div><div class="kpi-label">Aujourd'hui</div><div class="kpi-value"><?=(int)$stats['tickets_jour']?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-3">💰</div><div><div class="kpi-label">Recette jour</div><div class="kpi-value" style="font-size:1rem"><?=formatPrix((float)$stats['recette_jour'])?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-2">📋</div><div><div class="kpi-label">Total tickets</div><div class="kpi-value"><?=(int)$stats['total_tickets']?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-icon kp-4">💵</div><div><div class="kpi-label">Recette totale</div><div class="kpi-value" style="font-size:1rem"><?=formatPrix((float)$stats['recette_totale'])?></div></div></div></div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold small"><i class="bi bi-list-ul me-2 text-primary"></i>Liste des tickets</span>
        <span class="text-muted small">
          Page <?= $pageCourante ?> / <?= $totalPages ?>
          &nbsp;·&nbsp;
          <span class="badge bg-primary"><?= $totalItems ?> total</span>
        </span>
      </div>

      <?php if (empty($tickets)): ?>
        <div class="text-center text-muted py-5 small"><div class="fs-2 mb-2">🎫</div>Aucun ticket.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th><th>Étudiant</th><th>Matricule</th>
                <th>Date repas</th><th>Acheté le</th>
                <th>Montant</th><th>Paiement</th><th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickets as $t): ?>
                <tr>
                  <td class="text-muted small">#<?= $t['id_ticket'] ?></td>
                  <td class="fw-semibold small"><?= h($t['etudiant_nom']) ?></td>
                  <td class="small text-muted"><?= h($t['matricule']) ?></td>
                  <td class="small"><?= date('d/m/Y', strtotime($t['dateMenu'])) ?></td>
                  <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($t['dateAchat'])) ?></td>
                  <td class="fw-bold small text-success"><?= formatPrix((float)$t['montantTotal']) ?></td>
                  <td><span class="badge" style="background:rgba(58,90,64,.1);color:var(--c-green);font-size:.68rem"><?= $t['transactionD17'] ? 'D17' : 'Espèces' ?></span></td>
                  <td><span class="sbadge s-<?= $t['status'] ?>"><span class="sbadge-dot"></span><?= $t['status'] ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 py-3 px-4">
          <small class="text-muted">
            Affichage <?= $offset + 1 ?>–<?= min($offset + $parPage, $totalItems) ?> sur <?= $totalItems ?>
          </small>
          <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">

              <!-- Précédent -->
              <li class="page-item <?= $pageCourante <= 1 ? 'disabled' : '' ?>">
                <a class="page-link rounded" href="?page=<?= $pageCourante - 1 ?>">
                  <i class="bi bi-chevron-left"></i>
                </a>
              </li>

              <?php
              // Afficher max 5 numéros de pages centrés sur la page courante
              $debut = max(1, $pageCourante - 2);
              $fin   = min($totalPages, $pageCourante + 2);

              if ($debut > 1): ?>
                <li class="page-item">
                  <a class="page-link rounded" href="?page=1">1</a>
                </li>
                <?php if ($debut > 2): ?>
                  <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
              <?php endif; ?>

              <?php for ($p = $debut; $p <= $fin; $p++): ?>
                <li class="page-item <?= $p === $pageCourante ? 'active' : '' ?>">
                  <a class="page-link rounded" href="?page=<?= $p ?>"><?= $p ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($fin < $totalPages): ?>
                <?php if ($fin < $totalPages - 1): ?>
                  <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
                <li class="page-item">
                  <a class="page-link rounded" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                </li>
              <?php endif; ?>

              <!-- Suivant -->
              <li class="page-item <?= $pageCourante >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link rounded" href="?page=<?= $pageCourante + 1 ?>">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </li>

            </ul>
          </nav>
        </div>
        <?php endif; ?>

      <?php endif; ?>
    </div><!-- /card -->

  </div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php'; ?>