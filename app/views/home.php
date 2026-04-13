<?php require __DIR__ . '/partials/header.php'; ?>

<main>
  <section class="hero">
    <div class="hero-text">
      <h1>
        QA Assessment Game<br />
      </h1>
      <p class="description">
        Evaluez vos compétences en test logiciel avant entretien
      </p>
    </div>

    <div class="hero-image">
      <div class="image-card">
        <img src="assets/img/image_page_acceuil-removebg-preview.png" alt="Illustration étudiant" />
      </div>
    </div>

    <?php if (!empty($_SESSION['quiz_error'])): ?>
      <div style="
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        padding: 12px 18px;
        border-radius: 8px;
        margin: 20px auto;
        max-width: 700px;
        text-align: center;
        font-weight: 600;
      ">
        <?= htmlspecialchars($_SESSION['quiz_error']); ?>
      </div>
      <?php unset($_SESSION['quiz_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
      <a href="index.php?page=quiz_preview" class="btn-start" style="margin-bottom: 14px;">
        Voir le quiz
      </a>
    <?php endif; ?>

    <?php if (empty($quizAlreadyTaken)): ?>
      <a href="index.php?page=instructions" class="btn-start">
        Commence le quiz
      </a>
    <?php else: ?>
      <div style="
        margin-top: 15px;
        margin-bottom: 26px;
        background: #e5e7eb;
        color: #374151;
        padding: 14px 34px;
        border-radius: 999px;
        font-size: 16px;
        font-weight: 700;
        display: inline-block;
      ">
        Test déjà effectué
      </div>
    <?php endif; ?>
  </section>

  <section class="levels">
    <h2>Quiz d'entraînement</h2>

    <div class="level-cards">
      <div class="card beginner">
        <div class="card-top">
          <h3>Niveau Débutant</h3>
          <img src="assets/img/image_debutant.png" alt="Niveau débutant">
        </div>
        <div class="card-bottom">
          <p>Questions de base</p>
        </div>
      </div>

      <div class="card intermediate">
        <div class="card-top">
          <h3>Niveau Intermédiaire</h3>
          <img src="assets/img/image_intermediaire.png" alt="Niveau intermédiaire">
        </div>
        <div class="card-bottom">
          <p>Tests API &amp; SQL</p>
        </div>
      </div>

      <div class="card expert">
        <div class="card-top">
          <h3>Niveau Expert</h3>
          <img src="assets/img/image_expert.png" alt="Niveau expert">
        </div>
        <div class="card-bottom">
          <p>Scénarios avancés</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>