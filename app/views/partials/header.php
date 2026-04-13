<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCM Iteam Quality</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/home.css">
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
  <header class="navbar">
    <a href="index.php?page=home" class="logo">
      <img src="assets/img/logo_Iteam_quality.png" alt="Iteam Quality Logo">
      <h2><strong>ITEAM</strong> QUALITY</h2>
    </a>

    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="profile-menu">
        <button class="profile-btn" id="profileBtn" type="button">
          <span class="profile-avatar">👤</span>
          <span class="profile-name">
            <?= htmlspecialchars(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')) ?>
          </span>
          <span class="profile-chevron">▼</span>
        </button>

        <div class="profile-dropdown" id="profileDropdown">
          <a href="index.php?page=my_result" class="dropdown-link">Mon résultat</a>

          <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="index.php?page=admin_results" class="dropdown-link">Admin</a>
          <?php endif; ?>

          <a href="index.php?page=logout" class="dropdown-link logout-item">Déconnexion</a>
        </div>
      </div>
    <?php endif; ?>
  </header>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const profileBtn = document.getElementById('profileBtn');
      const profileDropdown = document.getElementById('profileDropdown');

      if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function () {
          profileDropdown.classList.remove('show');
        });
      }
    });
  </script>