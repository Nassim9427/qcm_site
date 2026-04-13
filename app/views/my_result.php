<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px;">
    <h1>Mon résultat</h1>

    <?php if (!empty($result)): ?>
        <?php
            $score = (int)$result['score'];
            $total = (int)$result['total_questions'];
            $pourcentage = $total > 0 ? round(($score / $total) * 100, 2) : 0;
        ?>

        <div style="
            max-width: 700px;
            margin: 30px auto 0;
            background: white;
            color: black;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            text-align: center;
        ">
            <h2 style="margin-top: 0; color: #f97316;">Résultat du test</h2>

            <p style="font-size: 22px; font-weight: bold; margin: 20px 0 10px;">
                Score : <?= htmlspecialchars($score) ?> / <?= htmlspecialchars($total) ?>
            </p>

            <p style="font-size: 20px; margin: 10px 0;">
                Pourcentage de bonnes réponses : 
                <span style="color: #f97316; font-weight: bold;"><?= htmlspecialchars($pourcentage) ?>%</span>
            </p>

            <p style="margin-top: 18px; color: #555;">
                Date du test : <?= htmlspecialchars($result['created_at']) ?>
            </p>

            <a href="index.php?page=home" style="
                display:inline-block;
                margin-top:25px;
                background:#f97316;
                color:white;
                padding:12px 24px;
                border-radius:999px;
                text-decoration:none;
                font-weight:700;
            ">
                Retour à l'accueil
            </a>
        </div>
    <?php else: ?>
        <div style="
            max-width: 700px;
            margin: 30px auto 0;
            background: white;
            color: black;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            text-align: center;
        ">
            <h2 style="margin-top: 0; color: #f97316;">Aucun résultat</h2>
            <p>Vous n'avez pas encore passé le test.</p>

            <a href="index.php?page=home" style="
                display:inline-block;
                margin-top:25px;
                background:#f97316;
                color:white;
                padding:12px 24px;
                border-radius:999px;
                text-decoration:none;
                font-weight:700;
            ">
                Retour à l'accueil
            </a>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>