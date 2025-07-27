<!-- <?php
// $currentPage = basename($_SERVER['PHP_SELF']);
?> -->




<div class="sidebar">
  <div class="top">
    <h2>Smartphone App</h2>
    <a href="liste.php" class="<?= $currentPage == 'liste.php' ? 'active' : '' ?>">
      <i class="ri-list-indefinite"></i> Smartphones
    </a>
  </div>

  <div class="bottom">
    <?php if ($role === 'admin'): ?>
      <div class="admin-only">
        <a href="parametres.php" class="<?= $currentPage == 'parametres.php' ? 'active' : '' ?>">
          <i class="ri-settings-3-fill"></i> Paramètres
        </a>
      </div>
    <?php endif; ?>
    <a href="compte.php" class="<?= $currentPage == 'compte.php' ? 'active' : '' ?>">
      <i class="ri-user-3-fill"></i> Mon compte
    </a>

    <a href="logout.php" onclick="return confirm('Se déconnecter ?');">
      <i class="ri-logout-box-line"></i> Déconnexion
    </a>
  </div>
</div>