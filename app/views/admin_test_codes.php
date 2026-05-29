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
            margin-bottom:24px;
        ">
            <div>
                <h1 style="margin:0 0 8px; color:#111827; font-size:34px;">
                    Mots de passe des tests
                </h1>
                <p style="margin:0; color:#4b5563;">
                    Consulte et modifie les codes nécessaires pour accéder aux tests par notion.
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

        <?php if (!empty($_SESSION['test_code_success'])): ?>
            <div style="
                background:#dcfce7;
                color:#166534;
                border:1px solid #86efac;
                padding:14px 18px;
                border-radius:12px;
                font-weight:700;
                margin-bottom:18px;
            ">
                <?= htmlspecialchars($_SESSION['test_code_success']) ?>
            </div>
            <?php unset($_SESSION['test_code_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['test_code_error'])): ?>
            <div style="
                background:#fee2e2;
                color:#991b1b;
                border:1px solid #fca5a5;
                padding:14px 18px;
                border-radius:12px;
                font-weight:700;
                margin-bottom:18px;
            ">
                <?= htmlspecialchars($_SESSION['test_code_error']) ?>
            </div>
            <?php unset($_SESSION['test_code_error']); ?>
        <?php endif; ?>

        <div style="display:grid; gap:18px;">
            <?php foreach ($testCodes as $testCode): ?>
                <form method="POST" action="index.php?page=admin_update_test_code" style="
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    padding:20px;
                    background:#f9fafb;
                ">
                    <input type="hidden" name="test_key" value="<?= htmlspecialchars($testCode['test_key']) ?>">

                    <div style="
                        display:grid;
                        grid-template-columns: 1.5fr 1fr auto;
                        gap:16px;
                        align-items:end;
                    ">
                        <div>
                            <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">
                                Test
                            </label>
                            <div style="
                                background:white;
                                border:1px solid #d1d5db;
                                border-radius:14px;
                                padding:14px 16px;
                                color:#111827;
                                font-weight:700;
                            ">
                                <?= htmlspecialchars($testCode['label']) ?>
                            </div>

                            <div style="font-size:13px; color:#6b7280; margin-top:8px;">
                                Dernière modification :
                                <?= !empty($testCode['updated_at']) ? htmlspecialchars($testCode['updated_at']) : 'Non renseignée' ?>
                            </div>
                        </div>

                        <div>
                            <label style="display:block; font-weight:800; color:#111827; margin-bottom:8px;">
                                Mot de passe actuel
                            </label>
                            <input
                                type="text"
                                name="new_code"
                                value="<?= htmlspecialchars($testCode['code_value'] ?? '') ?>"
                                required
                                style="
                                    width:100%;
                                    box-sizing:border-box;
                                    border:1px solid #d1d5db;
                                    border-radius:14px;
                                    padding:14px 16px;
                                    font:inherit;
                                "
                            >
                        </div>

                        <button type="submit" style="
                            background:#f97316;
                            color:white;
                            border:none;
                            padding:14px 22px;
                            border-radius:999px;
                            font-weight:800;
                            cursor:pointer;
                            white-space:nowrap;
                        ">
                            Modifier
                        </button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>