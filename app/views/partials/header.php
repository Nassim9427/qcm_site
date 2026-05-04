<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;

$prenom = $_SESSION['prenom'] ?? $_SESSION['user_prenom'] ?? '';
$nom = $_SESSION['nom'] ?? $_SESSION['user_nom'] ?? '';
$email = $_SESSION['email'] ?? '';

$displayName = trim($prenom . ' ' . $nom);
if ($displayName === '') {
    $displayName = $email !== '' ? $email : 'Utilisateur';
}

$initial = mb_strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'), 'UTF-8');
?>
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

    <?php if ($isLoggedIn): ?>
      <div class="profile-menu">
        <button class="profile-btn" id="profileBtn" type="button">
          <span class="profile-avatar"><?= htmlspecialchars($initial) ?></span>

          <span class="profile-meta">
            <span class="profile-name"><?= htmlspecialchars($displayName) ?></span>
          </span>

          <span class="profile-chevron">▼</span>
        </button>

        <div class="profile-dropdown" id="profileDropdown">
          <div class="dropdown-head">
            <?php if ($email !== ''): ?>
              <div class="dropdown-user-email">
                <?= htmlspecialchars($email) ?>
              </div>
            <?php endif; ?>
          </div>

          <a href="index.php?page=home" class="dropdown-link">Accueil</a>

          <?php if (!$isAdmin): ?>
            <a href="index.php?page=my_result" class="dropdown-link">Ma correction</a>
          <?php endif; ?>

          <?php if ($isAdmin): ?>
            <a href="index.php?page=quiz_preview" class="dropdown-link">Gérer le quiz</a>
            <a href="index.php?page=admin_access_keys" class="dropdown-link">Clés d'accès</a>
            <a href="index.php?page=admin_users" class="dropdown-link">Gestion des utilisateurs</a>
            <a href="index.php?page=admin_results" class="dropdown-link">Résultats</a>
            <a href="index.php?page=admin_stats" class="dropdown-link">Statistiques</a>
          <?php endif; ?>

          <a href="index.php?page=logout" class="dropdown-link logout-item">Déconnexion</a>
        </div>
      </div>
    <?php else: ?>
      <div class="header-public-links">
        <a href="index.php?page=access_login" class="login-btn">Connexion candidat</a>
        <a href="index.php?page=admin_login" class="admin-link">Admin</a>
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

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            profileDropdown.classList.remove('show');
          }
        });
      }
    });
  </script>