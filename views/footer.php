<?php // views/footer.php ?>
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
        <p class="small mb-1 opacity-75">🌅 Petit-déjeuner : 7h00 – 9h00</p>
        <p class="small mb-1 opacity-75">☀️ Déjeuner : 12h00 – 14h30</p>
        <p class="small mb-0 opacity-75">🌙 Dîner : 19h00 – 21h00</p>
      </div>
    </div>
    <hr style="border-color:rgba(255,255,255,.1);margin:2rem 0 1rem">
    <p class="text-center mb-0 small opacity-50">© <?= date('Y') ?> Resto ESEN · Projet Intégré 2BI · Université de La Manouba</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
</body>
</html>
