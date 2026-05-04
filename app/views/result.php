<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$score = isset($result['score']) ? (int)$result['score'] : (isset($_SESSION['quiz_score']) ? (int)$_SESSION['quiz_score'] : 0);
$totalQuestions = isset($result['total_questions']) ? (int)$result['total_questions'] : (isset($_SESSION['quiz_total_questions']) ? (int)$_SESSION['quiz_total_questions'] : 0);
$percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;
$isAdmin = isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
?>

<main style="padding: 40px 20px; background: #f5f5f5; min-height: calc(100vh - 120px);">
    <section style="
        max-width: 800px;
        margin: 0 auto;
        background: white;
        color: black;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    ">
        <div style="
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 34px 30px;
            text-align: center;
        ">
            <div style="
                width: 84px;
                height: 84px;
                margin: 0 auto 18px;
                border-radius: 999px;
                background: rgba(255,255,255,0.18);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 42px;
            ">
                ✅
            </div>

            <h1 style="margin: 0 0 10px; font-size: 34px;">
                Quiz envoyé
            </h1>

            <p style="margin: 0; font-size: 16px; opacity: 0.96;">
                Votre questionnaire a bien été enregistré.
            </p>
        </div>

        <div style="padding: 32px 30px;">
            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 18px;
                margin-bottom: 28px;
            ">
                <div style="
                    background: #fff7ed;
                    border: 1px solid #fdba74;
                    border-radius: 18px;
                    padding: 22px;
                    text-align: center;
                ">
                    <div style="font-size: 14px; color: #9a3412; font-weight: 700; margin-bottom: 8px;">
                        Score
                    </div>
                    <div style="font-size: 34px; font-weight: 800; color: #ea580c;">
                        <?= $score ?>/<?= $totalQuestions ?>
                    </div>
                </div>

                <div style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 22px;
                    text-align: center;
                ">
                    <div style="font-size: 14px; color: #6b7280; font-weight: 700; margin-bottom: 8px;">
                        Bonnes réponses
                    </div>
                    <div style="font-size: 34px; font-weight: 800; color: #111827;">
                        <?= $score ?>
                    </div>
                </div>

                <div style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 22px;
                    text-align: center;
                ">
                    <div style="font-size: 14px; color: #6b7280; font-weight: 700; margin-bottom: 8px;">
                        Pourcentage
                    </div>
                    <div style="font-size: 34px; font-weight: 800; color: #111827;">
                        <?= $percentage ?>%
                    </div>
                </div>
            </div>

            <div style="
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                padding: 22px;
                margin-bottom: 28px;
            ">
                <h2 style="margin-top: 0; margin-bottom: 12px; font-size: 20px; color: #111827;">
                    Résumé
                </h2>

                <p style="margin: 0; color: #374151; line-height: 1.8;">
                    Vous avez obtenu <strong><?= $score ?></strong> bonne(s) réponse(s) sur
                    <strong><?= $totalQuestions ?></strong>, soit un taux de réussite de
                    <strong><?= $percentage ?>%</strong>.
                </p>
            </div>

            <div style="
                display: flex;
                justify-content: center;
                gap: 14px;
                flex-wrap: wrap;
                border-top: 1px solid #e5e7eb;
                padding-top: 24px;
            ">
                <a href="index.php?page=home" style="
                    display: inline-block;
                    text-decoration: none;
                    background: #f97316;
                    color: white;
                    padding: 14px 24px;
                    border-radius: 999px;
                    font-weight: 700;
                    box-shadow: 0 8px 18px rgba(249, 115, 22, 0.25);
                ">
                    Retour à l'accueil
                </a>

                <a href="index.php?page=my_result" style="
                    display: inline-block;
                    text-decoration: none;
                    background: #e5e7eb;
                    color: #374151;
                    padding: 14px 24px;
                    border-radius: 999px;
                    font-weight: 700;
                ">
                    Voir la correction détaillée
                </a>

                <?php if ($isAdmin): ?>
                    <a href="index.php?page=instructions" style="
                        display: inline-block;
                        text-decoration: none;
                        background: #e5e7eb;
                        color: #374151;
                        padding: 14px 24px;
                        border-radius: 999px;
                        font-weight: 700;
                    ">
                        Refaire le quiz
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>