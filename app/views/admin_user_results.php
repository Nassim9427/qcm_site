<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$testMode = $_GET['test_mode'] ?? '';

$testModes = [
    '' => 'Toutes les notions',
    'all' => 'Test complet',
    'project' => 'Gestion de projet',
    'sql' => 'SQL',
    'api' => 'API',
    'moa' => 'MOA / Business Analyst',
    'technique' => 'Technique et automatisation',
    'logic' => 'Logique et cas pratiques',
];

$fullName = trim(($candidate['prenom'] ?? '') . ' ' . ($candidate['nom'] ?? ''));
if ($fullName === '') {
    $fullName = 'Candidat';
}
?>

<main style="padding:40px 20px; background:#f5f5f5; min-height:calc(100vh - 120px);">
    <section style="max-width:1100px; margin:0 auto;">
        <div style="
            background:white;
            border-radius:24px;
            padding:30px;
            box-shadow:0 12px 30px rgba(0,0,0,0.08);
            margin-bottom:24px;
        ">
            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
                flex-wrap:wrap;
                margin-bottom:20px;
            ">
                <div>
                    <h1 style="margin:0 0 8px; color:#111827; font-size:36px; font-weight:900;">
                        Tests de <?= htmlspecialchars($fullName) ?>
                    </h1>

                    <p style="margin:0; color:#4b5563;">
                        <?= htmlspecialchars($candidate['email'] ?? '') ?>
                    </p>
                </div>

                <a href="index.php?page=admin_results" style="
                    display:inline-block;
                    text-decoration:none;
                    background:#f97316;
                    color:white;
                    padding:12px 20px;
                    border-radius:999px;
                    font-weight:800;
                ">
                    Retour aux résultats
                </a>
            </div>

            <form method="GET" action="index.php" style="
                background:#f9fafb;
                border:1px solid #e5e7eb;
                border-radius:18px;
                padding:18px;
            ">
                <input type="hidden" name="page" value="admin_user_results">
                <input type="hidden" name="user_id" value="<?= (int)$candidate['id'] ?>">

                <div style="
                    display:grid;
                    grid-template-columns:1fr auto auto;
                    gap:14px;
                    align-items:end;
                ">
                    <div>
                        <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">
                            Filtrer par notion / test
                        </label>
                        <select name="test_mode" style="
                            width:100%;
                            box-sizing:border-box;
                            padding:14px 16px;
                            border:1px solid #d1d5db;
                            border-radius:14px;
                            font:inherit;
                            background:white;
                        ">
                            <?php foreach ($testModes as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>" <?= $testMode === $value ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" style="
                        background:#f97316;
                        color:white;
                        border:none;
                        padding:14px 22px;
                        border-radius:999px;
                        font-weight:800;
                        cursor:pointer;
                    ">
                        Filtrer
                    </button>

                    <a href="index.php?page=admin_user_results&user_id=<?= (int)$candidate['id'] ?>" style="
                        display:inline-block;
                        text-decoration:none;
                        background:#e5e7eb;
                        color:#374151;
                        padding:14px 22px;
                        border-radius:999px;
                        font-weight:800;
                    ">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <?php if (empty($results)): ?>
            <div style="
                background:white;
                border-radius:20px;
                padding:28px;
                box-shadow:0 10px 26px rgba(0,0,0,0.07);
                text-align:center;
                color:#374151;
                font-weight:800;
            ">
                Aucun test trouvé pour ce candidat.
            </div>
        <?php else: ?>
            <div style="display:grid; gap:18px;">
                <?php foreach ($results as $result): ?>
                    <?php
                        $score = (int)($result['score'] ?? 0);
                        $total = (int)($result['total_questions'] ?? 0);
                        $percentage = $total > 0 ? round(($score / $total) * 100) : 0;
                        $testLabel = $result['test_label'] ?? 'Test';
                    ?>

                    <div style="
                        background:white;
                        border-radius:20px;
                        padding:22px;
                        box-shadow:0 10px 26px rgba(0,0,0,0.07);
                        display:grid;
                        grid-template-columns:1.5fr 130px 130px 170px 170px;
                        gap:16px;
                        align-items:center;
                    ">
                        <div>
                            <div style="
                                font-size:20px;
                                font-weight:900;
                                color:#111827;
                                margin-bottom:6px;
                            ">
                                <?= htmlspecialchars($testLabel) ?>
                            </div>

                            <div style="color:#6b7280; font-size:14px;">
                                Réalisé le :
                                <?= !empty($result['created_at']) ? htmlspecialchars($result['created_at']) : 'Date inconnue' ?>
                            </div>
                        </div>

                        <div style="
                            background:#fff7ed;
                            color:#c2410c;
                            border:1px solid #fdba74;
                            border-radius:14px;
                            padding:12px;
                            text-align:center;
                            font-weight:900;
                        ">
                            <?= $score ?>/<?= $total ?>
                        </div>

                        <div style="
                            background:#f9fafb;
                            color:#111827;
                            border:1px solid #e5e7eb;
                            border-radius:14px;
                            padding:12px;
                            text-align:center;
                            font-weight:900;
                        ">
                            <?= $percentage ?>%
                        </div>

                        <div style="
                            background:<?= $percentage >= 70 ? '#dcfce7' : ($percentage >= 50 ? '#fef3c7' : '#fee2e2') ?>;
                            color:<?= $percentage >= 70 ? '#166534' : ($percentage >= 50 ? '#92400e' : '#991b1b') ?>;
                            border-radius:999px;
                            padding:10px 14px;
                            text-align:center;
                            font-weight:900;
                        ">
                            <?= $percentage >= 70 ? 'Point fort' : ($percentage >= 50 ? 'Moyen' : 'À améliorer') ?>
                        </div>

                        <a href="index.php?page=admin_result_details&result_id=<?= (int)$result['id'] ?>" style="
                            display:inline-block;
                            text-decoration:none;
                            background:#f97316;
                            color:white;
                            border-radius:999px;
                            padding:12px 16px;
                            text-align:center;
                            font-weight:900;
                        ">
                            Voir détail
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>