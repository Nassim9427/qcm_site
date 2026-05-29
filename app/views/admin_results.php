<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$nom = $_GET['nom'] ?? '';
$prenom = $_GET['prenom'] ?? '';
$email = $_GET['email'] ?? '';
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

$totalCandidates = count($candidates ?? []);

$totalTests = 0;
$sumAverage = 0;
$bestAverage = null;

foreach (($candidates ?? []) as $candidate) {
    $totalTests += (int)($candidate['total_tests'] ?? 0);
    $avg = isset($candidate['average_percentage']) ? (float)$candidate['average_percentage'] : 0;
    $sumAverage += $avg;

    if ($bestAverage === null || $avg > $bestAverage) {
        $bestAverage = $avg;
    }
}

$globalAverage = $totalCandidates > 0 ? round($sumAverage / $totalCandidates) : 0;
$bestAverage = $bestAverage !== null ? round($bestAverage) : 0;
?>

<main style="padding:40px 20px; background:#f5f5f5; min-height:calc(100vh - 120px);">
    <section style="max-width:1200px; margin:0 auto;">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:20px;
            flex-wrap:wrap;
            margin-bottom:24px;
        ">
            <div>
                <h1 style="margin:0 0 8px; color:#111827; font-size:44px; font-weight:900;">
                    Résultats candidats
                </h1>
                <p style="margin:0; color:#4b5563; font-size:16px;">
                    Les résultats sont regroupés par candidat. Cliquez sur une personne pour voir ses tests réalisés.
                </p>
            </div>

            <a href="index.php?page=home" style="
                display:inline-block;
                text-decoration:none;
                background:#f97316;
                color:white;
                padding:14px 22px;
                border-radius:999px;
                font-weight:800;
                box-shadow:0 8px 18px rgba(249,115,22,0.25);
            ">
                Retour à l'accueil
            </a>
        </div>

        <?php if (!empty($_SESSION['admin_results_error'])): ?>
            <div style="
                background:#fee2e2;
                color:#991b1b;
                border:1px solid #fca5a5;
                padding:14px 18px;
                border-radius:12px;
                font-weight:700;
                margin-bottom:18px;
            ">
                <?= htmlspecialchars($_SESSION['admin_results_error']) ?>
            </div>
            <?php unset($_SESSION['admin_results_error']); ?>
        <?php endif; ?>

        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
            gap:18px;
            margin-bottom:24px;
        ">
            <div style="background:white; border-radius:20px; padding:22px; box-shadow:0 10px 26px rgba(0,0,0,0.07);">
                <div style="color:#6b7280; font-weight:800; margin-bottom:8px;">Candidats</div>
                <div style="font-size:34px; font-weight:900; color:#111827;"><?= (int)$totalCandidates ?></div>
            </div>

            <div style="background:white; border-radius:20px; padding:22px; box-shadow:0 10px 26px rgba(0,0,0,0.07);">
                <div style="color:#6b7280; font-weight:800; margin-bottom:8px;">Tests réalisés</div>
                <div style="font-size:34px; font-weight:900; color:#111827;"><?= (int)$totalTests ?></div>
            </div>

            <div style="background:white; border-radius:20px; padding:22px; box-shadow:0 10px 26px rgba(0,0,0,0.07);">
                <div style="color:#6b7280; font-weight:800; margin-bottom:8px;">Moyenne candidats</div>
                <div style="font-size:34px; font-weight:900; color:#ea580c;"><?= (int)$globalAverage ?>%</div>
            </div>

            <div style="background:white; border-radius:20px; padding:22px; box-shadow:0 10px 26px rgba(0,0,0,0.07);">
                <div style="color:#6b7280; font-weight:800; margin-bottom:8px;">Meilleure moyenne</div>
                <div style="font-size:34px; font-weight:900; color:#16a34a;"><?= (int)$bestAverage ?>%</div>
            </div>
        </div>

        <form method="GET" action="index.php" style="
            background:white;
            border-radius:22px;
            padding:22px;
            box-shadow:0 10px 26px rgba(0,0,0,0.07);
            margin-bottom:24px;
        ">
            <input type="hidden" name="page" value="admin_results">

            <div style="
                display:grid;
                grid-template-columns:repeat(auto-fit, minmax(190px, 1fr));
                gap:16px;
                align-items:end;
            ">
                <div>
                    <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" placeholder="Nom" style="
                        width:100%;
                        box-sizing:border-box;
                        padding:14px 16px;
                        border:1px solid #d1d5db;
                        border-radius:14px;
                        font:inherit;
                    ">
                </div>

                <div>
                    <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" placeholder="Prénom" style="
                        width:100%;
                        box-sizing:border-box;
                        padding:14px 16px;
                        border:1px solid #d1d5db;
                        border-radius:14px;
                        font:inherit;
                    ">
                </div>

                <div>
                    <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">Email</label>
                    <input type="text" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" style="
                        width:100%;
                        box-sizing:border-box;
                        padding:14px 16px;
                        border:1px solid #d1d5db;
                        border-radius:14px;
                        font:inherit;
                    ">
                </div>

                <div>
                    <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">Notion / test</label>
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

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
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

                    <a href="index.php?page=admin_results" style="
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
            </div>
        </form>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow:0 10px 26px rgba(0,0,0,0.07);
            overflow:hidden;
        ">
            <?php if (empty($candidates)): ?>
                <div style="padding:28px; color:#374151; font-weight:800; text-align:center;">
                    Aucun résultat trouvé.
                </div>
            <?php else: ?>
                <div style="
                    display:grid;
                    grid-template-columns:1.5fr 1.6fr 130px 150px 160px 160px;
                    gap:0;
                    background:#f8fafc;
                    border-bottom:1px solid #e5e7eb;
                    color:#374151;
                    font-weight:900;
                    font-size:14px;
                ">
                    <div style="padding:16px;">Candidat</div>
                    <div style="padding:16px;">Email</div>
                    <div style="padding:16px; text-align:center;">Tests</div>
                    <div style="padding:16px; text-align:center;">Moyenne</div>
                    <div style="padding:16px; text-align:center;">Dernier test</div>
                    <div style="padding:16px; text-align:center;">Action</div>
                </div>

                <?php foreach ($candidates as $candidate): ?>
                    <?php
                        $userId = (int)$candidate['user_id'];
                        $fullName = trim(($candidate['prenom'] ?? '') . ' ' . ($candidate['nom'] ?? ''));
                        if ($fullName === '') {
                            $fullName = 'Candidat';
                        }

                        $avg = isset($candidate['average_percentage']) ? round((float)$candidate['average_percentage']) : 0;
                    ?>

                    <div style="
                        display:grid;
                        grid-template-columns:1.5fr 1.6fr 130px 150px 160px 160px;
                        gap:0;
                        border-bottom:1px solid #e5e7eb;
                        align-items:center;
                    ">
                        <div style="padding:16px;">
                            <div style="font-weight:900; color:#111827; font-size:16px;">
                                <?= htmlspecialchars($fullName) ?>
                            </div>
                        </div>

                        <div style="padding:16px; color:#4b5563;">
                            <?= htmlspecialchars($candidate['email'] ?? '') ?>
                        </div>

                        <div style="padding:16px; text-align:center;">
                            <span style="
                                display:inline-block;
                                background:#fff7ed;
                                color:#c2410c;
                                border:1px solid #fdba74;
                                padding:8px 12px;
                                border-radius:999px;
                                font-weight:900;
                            ">
                                <?= (int)$candidate['total_tests'] ?>
                            </span>
                        </div>

                        <div style="padding:16px; text-align:center; font-weight:900; color:#111827;">
                            <?= (int)$avg ?>%
                        </div>

                        <div style="padding:16px; text-align:center; color:#6b7280; font-size:13px;">
                            <?= !empty($candidate['last_test_date']) ? htmlspecialchars($candidate['last_test_date']) : '-' ?>
                        </div>

                        <div style="padding:16px; text-align:center;">
                            <a href="index.php?page=admin_user_results&user_id=<?= $userId ?><?= $testMode !== '' ? '&test_mode=' . urlencode($testMode) : '' ?>" style="
                                display:inline-block;
                                text-decoration:none;
                                background:#f97316;
                                color:white;
                                padding:10px 16px;
                                border-radius:999px;
                                font-weight:900;
                                font-size:14px;
                            ">
                                Voir les tests
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>