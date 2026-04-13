<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px;">
    <h1>Résultats des candidats</h1>

    <!-- FILTRES -->
    <form method="GET" action="index.php" style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="hidden" name="page" value="admin_results">

        <input type="text" name="nom" placeholder="Filtrer par nom"
               value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>"
               style="padding:8px; border-radius:6px; border:1px solid #ccc;">

        <input type="text" name="prenom" placeholder="Filtrer par prénom"
               value="<?= htmlspecialchars($_GET['prenom'] ?? '') ?>"
               style="padding:8px; border-radius:6px; border:1px solid #ccc;">

        <input type="text" name="email" placeholder="Filtrer par email"
               value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
               style="padding:8px; border-radius:6px; border:1px solid #ccc;">

        <button type="submit"
                style="background:#f97316; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
            Rechercher
        </button>
    </form>

    <!-- BOUTON STATISTIQUES -->
    <a href="index.php?page=admin_stats"
       style="display:inline-block; margin-bottom:20px; background:#f97316; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:bold;">
        Voir les statistiques
    </a>

    <!-- TABLEAU -->
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
                <th style="background:#f97316; color:white; padding:10px;">ID</th>
                <th style="background:#f97316; color:white; padding:10px;">Nom</th>
                <th style="background:#f97316; color:white; padding:10px;">Prénom</th>
                <th style="background:#f97316; color:white; padding:10px;">Email</th>
                <th style="background:#f97316; color:white; padding:10px;">Score</th>
                <th style="background:#f97316; color:white; padding:10px;">Total</th>
                <th style="background:#f97316; color:white; padding:10px;">Date</th>
                <th style="background:#f97316; color:white; padding:10px;">Détail</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $result): ?>
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:10px;"><?= htmlspecialchars($result['id']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($result['nom']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($result['prenom']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($result['email']) ?></td>
                        <td style="padding:10px; font-weight:bold;"><?= htmlspecialchars($result['score']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($result['total_questions']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($result['created_at']) ?></td>
                        <td style="padding:10px;">
                            <a href="index.php?page=admin_result_details&result_id=<?= $result['id'] ?>"
                               style="color:#2563eb; font-weight:bold;">
                                Voir réponses
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding:20px;">
                        Aucun résultat trouvé.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>