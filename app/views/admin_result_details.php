<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px;">
    <h1>Détail des réponses</h1>

    <a href="index.php?page=admin_results"
       style="display:inline-block; margin-bottom:20px; color:#2563eb; font-weight:bold;">
        ← Retour aux résultats
    </a>

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
                <th style="background:#f97316; color:white; padding:10px;">Question</th>
                <th style="background:#f97316; color:white; padding:10px;">Réponse donnée</th>
                <th style="background:#f97316; color:white; padding:10px;">Bonne réponse</th>
                <th style="background:#f97316; color:white; padding:10px;">Correct ?</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($answers)): ?>
                <?php foreach ($answers as $answer): ?>
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:10px;">
                            <?= htmlspecialchars($answer['question_key']) ?>
                        </td>

                        <td style="padding:10px;">
                            <?= htmlspecialchars($answer['answer_given'] ?? 'Non répondu') ?>
                        </td>

                        <td style="padding:10px; font-weight:bold;">
                            <?= htmlspecialchars($answer['correct_answer']) ?>
                        </td>

                        <td style="padding:10px; font-weight:bold;">
                            <?php if ($answer['is_correct']): ?>
                                <span style="color:green;">✔️ Oui</span>
                            <?php else: ?>
                                <span style="color:red;">❌ Non</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding:20px;">
                        Aucune réponse trouvée.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>