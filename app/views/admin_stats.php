<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px;">
    <h1>Classement & Statistiques</h1>

    <!-- 🔥 BOUTON RETOUR -->
    <a href="index.php?page=admin_results"
       style="display:inline-block; margin-bottom:20px; color:#2563eb; font-weight:bold;">
        ← Retour aux résultats
    </a>

    <div style="display:flex; gap:20px; flex-wrap:wrap; margin: 25px 0;">
        <div style="background:white; color:black; padding:20px; border-radius:12px; min-width:220px;">
            <h3 style="margin-top:0; color:#f97316;">Total des tests</h3>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?= htmlspecialchars($stats['total_tests'] ?? 0) ?>
            </p>
        </div>

        <div style="background:white; color:black; padding:20px; border-radius:12px; min-width:220px;">
            <h3 style="margin-top:0; color:#f97316;">Score moyen</h3>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?= number_format((float)($stats['moyenne_score'] ?? 0), 2) ?>
            </p>
        </div>

        <div style="background:white; color:black; padding:20px; border-radius:12px; min-width:220px;">
            <h3 style="margin-top:0; color:#f97316;">Meilleur score</h3>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?= htmlspecialchars($stats['meilleur_score'] ?? 0) ?>
            </p>
        </div>
    </div>

    <h2 style="margin-top:30px;">Classement des candidats</h2>

    <table style="
        width:100%;
        border-collapse:collapse;
        background:white;
        color:black;
        text-align:center;
        border-radius:10px;
        overflow:hidden;
    ">
        <thead>
            <tr>
                <th style="background:#f97316; color:white; padding:10px;">Rang</th>
                <th style="background:#f97316; color:white; padding:10px;">Nom</th>
                <th style="background:#f97316; color:white; padding:10px;">Prénom</th>
                <th style="background:#f97316; color:white; padding:10px;">Email</th>
                <th style="background:#f97316; color:white; padding:10px;">Score</th>
                <th style="background:#f97316; color:white; padding:10px;">Total</th>
                <th style="background:#f97316; color:white; padding:10px;">Date</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($ranking)): ?>
                <?php foreach ($ranking as $index => $row): ?>
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:10px; font-weight:bold;"><?= $index + 1 ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($row['nom']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($row['prenom']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($row['email']) ?></td>
                        <td style="padding:10px; font-weight:bold;"><?= htmlspecialchars($row['score']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($row['total_questions']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding:20px;">Aucune donnée disponible.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>