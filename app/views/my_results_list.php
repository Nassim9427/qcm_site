<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding:40px 20px; background:#f5f5f5; min-height:calc(100vh - 120px);">
    <section style="
        max-width:1000px;
        margin:0 auto;
        background:white;
        border-radius:24px;
        padding:30px;
        box-shadow:0 12px 30px rgba(0,0,0,0.08);
    ">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:20px;
            flex-wrap:wrap;
            margin-bottom:26px;
        ">
            <div>
                <h1 style="margin:0 0 8px; color:#111827; font-size:36px;">
                    Mes corrections
                </h1>
                <p style="margin:0; color:#4b5563; font-size:16px;">
                    Choisissez le test dont vous souhaitez consulter la correction détaillée.
                </p>
            </div>

            <a href="index.php?page=home" style="
                display:inline-block;
                text-decoration:none;
                background:#f97316;
                color:white;
                padding:12px 20px;
                border-radius:999px;
                font-weight:700;
            ">
                Retour à l'accueil
            </a>
        </div>

        <?php if (!empty($_SESSION['quiz_error'])): ?>
            <div style="
                background:#fee2e2;
                color:#991b1b;
                border:1px solid #fca5a5;
                padding:14px 18px;
                border-radius:12px;
                font-weight:700;
                margin-bottom:18px;
            ">
                <?= htmlspecialchars($_SESSION['quiz_error']) ?>
            </div>
            <?php unset($_SESSION['quiz_error']); ?>
        <?php endif; ?>

        <?php if (empty($results)): ?>
            <div style="
                background:#f9fafb;
                border:1px solid #e5e7eb;
                border-radius:18px;
                padding:24px;
                color:#374151;
                font-weight:700;
                text-align:center;
            ">
                Aucun test réalisé pour le moment.
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

                    <a href="index.php?page=my_result&result_id=<?= (int)$result['id'] ?>" style="
                        display:block;
                        text-decoration:none;
                        color:inherit;
                        background:#f9fafb;
                        border:1px solid #e5e7eb;
                        border-radius:20px;
                        padding:22px;
                        transition:0.2s ease;
                    ">
                        <div style="
                            display:grid;
                            grid-template-columns: 1.4fr 120px 120px 160px;
                            gap:16px;
                            align-items:center;
                        ">
                            <div>
                                <div style="
                                    font-size:20px;
                                    font-weight:800;
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
                                font-weight:800;
                            ">
                                <?= $score ?>/<?= $total ?>
                            </div>

                            <div style="
                                background:white;
                                color:#111827;
                                border:1px solid #e5e7eb;
                                border-radius:14px;
                                padding:12px;
                                text-align:center;
                                font-weight:800;
                            ">
                                <?= $percentage ?>%
                            </div>

                            <div style="
                                background:#f97316;
                                color:white;
                                border-radius:999px;
                                padding:12px 16px;
                                text-align:center;
                                font-weight:800;
                            ">
                                Voir correction
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>