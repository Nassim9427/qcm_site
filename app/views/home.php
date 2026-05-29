<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$isAdmin = isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;

$takenTestModes = $takenTestModes ?? [];
$quizAlreadyTaken = in_array('all', $takenTestModes, true);

$testCards = [
  [
    'slug' => 'project',
    'title' => 'Gestion de projet',
    'description' => 'Agile et Cycle en V',
    'class' => 'project',
    'image' => 'assets/img/image_Gestionprojet.png',
    'alt' => 'Gestion de projet'
  ],
  [
    'slug' => 'sql',
    'title' => 'SQL',
    'description' => 'Requêtes et logique',
    'class' => 'sql',
    'image' => 'assets/img/image_sql.png',
    'alt' => 'SQL'
  ],
  [
    'slug' => 'api',
    'title' => 'API',
    'description' => 'Webservices et échanges',
    'class' => 'api',
    'image' => 'assets/img/image_api.png',
    'alt' => 'API'
  ],
  [
    'slug' => 'moa',
    'title' => 'MOA / Business Analyst',
    'description' => 'User stories, backlog, analyse métier',
    'class' => 'moa',
    'image' => 'assets/img/image_moa.png',
    'alt' => 'MOA Business Analyst'
  ],
  [
    'slug' => 'technique',
    'title' => 'Technique & automatisation',
    'description' => 'Selenium, Docker, CI/CD',
    'class' => 'technique',
    'image' => 'assets/img/image_technique.png',
    'alt' => 'Technique et automatisation'
  ],
  [
    'slug' => 'logic',
    'title' => 'Logique & cas pratiques',
    'description' => 'Mises en situation QA',
    'class' => 'logic',
    'image' => 'assets/img/image_logique.png',
    'alt' => 'Logique et cas pratiques'
  ],
];
?>

<style>
  .home-page {
    min-height: calc(100vh - 90px);
    padding: 20px 20px 40px;
  }

  .hero {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    text-align: center;
    padding: 10px 20px 0;
  }

  .hero-text {
    max-width: 700px;
  }

  .hero h1 {
    font-size: 48px;
    font-weight: 800;
    letter-spacing: 1px;
    margin: 0 0 10px;
    color: white;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  }

  .hero h1::after {
    content: "";
    display: block;
    width: 300px;
    height: 3px;
    margin: 12px auto 0;
    background: linear-gradient(90deg, #60a5fa, #93c5fd);
    border-radius: 2px;
  }

  .description {
    opacity: 0.9;
    font-size: 14px;
    margin: 0 0 8px;
    color: white;
  }

  .hero-image {
    display: flex;
    justify-content: center;
    margin: 0;
  }

  .hero-image img {
    max-width: 300px;
    width: 100%;
    display: block;
  }

  .btn-start {
    display: inline-block;
    margin-top: 8px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white !important;
    padding: 14px 34px;
    border-radius: 999px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none !important;
    box-shadow: 0 8px 18px rgba(234, 88, 12, 0.25);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .btn-start:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 22px rgba(234, 88, 12, 0.35);
    text-decoration: none !important;
  }

  .quiz-done-pill {
    margin-top: 15px;
    margin-bottom: 26px;
    background: #e5e7eb;
    color: #374151;
    padding: 14px 34px;
    border-radius: 999px;
    font-size: 16px;
    font-weight: 700;
    display: inline-block;
  }

  .admin-actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
    align-items: center;
    margin-bottom: 14px;
  }

  .notion-tests {
    text-align: center;
    margin: 0 22px 30px;
  }

  .notion-tests h2 {
    margin: 0 0 8px;
    color: white;
    font-size: 28px;
    font-weight: 800;
  }

  .notion-tests-subtitle {
    margin: 0 auto 18px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 14px;
    max-width: 700px;
    line-height: 1.5;
  }

  .notion-cards {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 0;
  }

  .notion-card {
    width: 240px;
    height: auto;
    border-radius: 18px;
    overflow: hidden;
    background: white;
    text-decoration: none !important;
    color: inherit;
    display: block;
    flex: 0 0 240px;
    transition: transform 0.25s ease, box-shadow 0.25s ease, opacity 0.25s ease;
  }

  .notion-card,
  .notion-card:visited,
  .notion-card:hover,
  .notion-card:active {
    text-decoration: none !important;
    color: inherit;
  }

  .notion-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 18px rgba(0, 0, 0, 0.14);
  }

  .notion-card-top {
    height: 145px;
    padding: 16px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
    box-sizing: border-box;
  }

  .notion-card-top::after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: -5%;
    width: 110%;
    height: 55px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50% 50% 0 0;
    z-index: 1;
  }

  .notion-card h3 {
    position: relative;
    z-index: 2;
    font-size: 14px;
    margin: 0 0 10px;
    color: white;
    font-weight: 800;
    line-height: 1.2;
  }

  .notion-card-top img {
    display: block;
    margin: 14px auto 0;
    width: 80px;
    height: 80px;
    max-width: 90px;
    max-height: 90px;
    object-fit: contain;
    position: relative;
    z-index: 2;
    transition: transform 0.25s ease;
  }

  .notion-card:hover .notion-card-top img {
    transform: scale(1.04);
  }

  .notion-card-bottom {
    background: white;
    color: black;
    padding: 12px 10px;
    font-size: 13px;
    min-height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
  }

  .notion-card-bottom p {
    margin: 0;
    color: black;
    line-height: 1.4;
  }

  .notion-card-disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }

  .notion-card-disabled:hover {
    transform: none;
    box-shadow: none;
  }

  .notion-card.project .notion-card-top {
    background: linear-gradient(180deg, #1d91e0, #1576c6);
  }

  .notion-card.sql .notion-card-top {
    background: linear-gradient(180deg, #4d72df, #3557c4);
  }

  .notion-card.api .notion-card-top {
    background: linear-gradient(180deg, #5968a5, #44518d);
  }

  .notion-card.moa .notion-card-top {
    background: linear-gradient(180deg, #f97316, #ea580c);
  }

  .notion-card.technique .notion-card-top {
    background: linear-gradient(180deg, #16a34a, #15803d);
  }

  .notion-card.logic .notion-card-top {
    background: linear-gradient(180deg, #7c3aed, #5b21b6);
  }

  .test-done-badge {
    display: inline-block;
    margin-top: 6px;
    background: #e5e7eb;
    color: #374151;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
  }

  @media (max-width: 1024px) {
    .hero h1 {
      font-size: 40px;
    }

    .hero-image img {
      max-width: 250px;
    }

    .notion-card {
      width: 220px;
      flex-basis: 220px;
    }
  }

  @media (max-width: 768px) {
    .hero h1 {
      font-size: 34px;
    }

    .hero h1::after {
      width: 220px;
    }

    .description {
      font-size: 13px;
    }

    .hero-image img {
      max-width: 220px;
    }

    .btn-start {
      font-size: 15px;
      padding: 12px 26px;
    }

    .notion-card {
      width: 90%;
      max-width: 320px;
      flex: 0 0 90%;
    }
  }
</style>

<main class="home-page">
  <section class="hero">
    <div class="hero-text">
      <h1>QA Assessment Game</h1>
      <p class="description">
        Evaluez vos compétences en test logiciel avant entretien
      </p>
    </div>

    <div class="hero-image">
      <div class="image-card">
        <img src="assets/img/image_page_acceuil-removebg-preview.png" alt="Illustration étudiant">
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

    <?php if ($isAdmin): ?>
      <div class="admin-actions">
        <a href="index.php?page=quiz_preview" class="btn-start">
          Voir le quiz
        </a>

        <a href="index.php?page=admin_access_keys" class="btn-start">
          Gérer les clés d'accès
        </a>

        <a href="index.php?page=admin_test_codes" class="btn-start">
          Gérer les mots de passe des tests
        </a>

        <a href="index.php?page=choose_test&test=all" class="btn-start">
          Commence le quiz
        </a>
      </div>
    <?php else: ?>
      <?php if (!$quizAlreadyTaken): ?>
        <a href="index.php?page=choose_test&test=all" class="btn-start">
          Commence le quiz
        </a>
      <?php else: ?>
        <div class="quiz-done-pill">
          Test complet déjà effectué
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>

  <section class="notion-tests">
    <h2>Tests par notion</h2>
    <p class="notion-tests-subtitle">
      Choisissez une notion pour lancer un test ciblé. Chaque test ne peut être passé qu'une seule fois.
    </p>

    <div class="notion-cards">
      <?php foreach ($testCards as $card): ?>
        <?php $cardAlreadyTaken = in_array($card['slug'], $takenTestModes, true); ?>

        <?php if ($isAdmin || !$cardAlreadyTaken): ?>
          <a href="index.php?page=choose_test&test=<?= urlencode($card['slug']) ?>" class="notion-card <?= htmlspecialchars($card['class']) ?>">
            <div class="notion-card-top">
              <h3><?= htmlspecialchars($card['title']) ?></h3>
              <img src="<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['alt']) ?>">
            </div>

            <div class="notion-card-bottom">
              <p><?= htmlspecialchars($card['description']) ?></p>
            </div>
          </a>
        <?php else: ?>
          <div class="notion-card notion-card-disabled <?= htmlspecialchars($card['class']) ?>">
            <div class="notion-card-top">
              <h3><?= htmlspecialchars($card['title']) ?></h3>
              <img src="<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['alt']) ?>">
            </div>

            <div class="notion-card-bottom">
              <p>
                <?= htmlspecialchars($card['description']) ?><br>
                <span class="test-done-badge">Déjà effectué</span>
              </p>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>