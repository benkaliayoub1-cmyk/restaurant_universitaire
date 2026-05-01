<footer class="footer-dark mt-auto py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="footer-brand mb-2">🍽️ Resto ESEN</div>
        <p class="mb-0 small opacity-75">Restaurant universitaire numérique<br>École Supérieure d'Économie Numérique<br>Université de La Manouba</p>
      </div>
      <div class="col-md-4">
        <p class="text-white fw-semibold mb-2 small">Navigation</p>
        <a href="<?= APP_URL ?>/views/public/home.php"       class="footer-link">Accueil</a>
        <a href="<?= APP_URL ?>/views/public/menu.php"       class="footer-link">Menu du jour</a>
        <a href="<?= APP_URL ?>/views/public/historique.php" class="footer-link">Historique</a>
        <a href="<?= APP_URL ?>/views/public/register.php"   class="footer-link">Créer un compte</a>
      </div>
      <div class="col-md-4">
        <p class="text-white fw-semibold mb-2 small">Horaires</p>
        <p class="small mb-1 opacity-75">☀️ Déjeuner : 11h30 – 13h30</p>
        <p class="small mb-0 opacity-75">🌙 Dîner : 17h00 – 19h00</p>
      </div>
    </div>
    <hr style="border-color:rgba(255,255,255,.1);margin:2rem 0 1rem">
    <p class="text-center mb-0 small opacity-50">© <?= date('Y') ?> Resto ESEN · Université de La Manouba</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>

<!-- Modal confirmation déconnexion -->
<div class="modal fade" id="modalDeconnexion" tabindex="-1" aria-labelledby="modalDeconnexionLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:1rem;border:none;box-shadow:0 8px 32px rgba(0,0,0,.18);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalDeconnexionLabel">
          <i class="bi bi-box-arrow-right me-2 text-danger"></i>Déconnexion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body pt-2 pb-1">
        <p class="mb-0">Êtes-vous sûr de vouloir vous déconnecter ?</p>
      </div>
      <div class="modal-footer border-0 pt-2 gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Annuler
        </button>
        <a id="btnConfirmerDeconnexion" href="#" class="btn btn-danger">
          <i class="bi bi-box-arrow-right me-1"></i>Confirmer la déconnexion
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function confirmerDeconnexion(e) {
  e.preventDefault();
  var logoutUrl = '<?= APP_URL ?>/views/public/logout.php';
  document.getElementById('btnConfirmerDeconnexion').setAttribute('href', logoutUrl);
  var modal = new bootstrap.Modal(document.getElementById('modalDeconnexion'));
  modal.show();
}
</script>

</body>
</html>
