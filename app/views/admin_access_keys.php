<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px 20px; background: #f5f5f5; min-height: calc(100vh - 120px);">
    <section style="max-width: 1150px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:24px;">
            <div>
                <h1 style="margin:0 0 8px; font-size:42px; color:#111827;">Clés d'accès</h1>
                <p style="margin:0; color:#4b5563;">Génère des accès candidats par email, sans inscription publique.</p>
            </div>

            <a href="index.php?page=home" style="
                display:inline-block;
                text-decoration:none;
                background:#f97316;
                color:white;
                padding:14px 22px;
                border-radius:999px;
                font-weight:700;
                box-shadow: 0 8px 18px rgba(249, 115, 22, 0.2);
            ">
                Retour à l'accueil
            </a>
        </div>

        <?php if (!empty($_SESSION['admin_access_success'])): ?>
            <div style="
                background:#dcfce7;
                color:#166534;
                border:1px solid #86efac;
                padding:14px 18px;
                border-radius:12px;
                margin-bottom:18px;
                font-weight:700;
                word-break:break-word;
            ">
                <?= htmlspecialchars($_SESSION['admin_access_success']) ?>
            </div>
            <?php unset($_SESSION['admin_access_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['admin_access_error'])): ?>
            <div style="
                background:#fee2e2;
                color:#991b1b;
                border:1px solid #fca5a5;
                padding:14px 18px;
                border-radius:12px;
                margin-bottom:18px;
                font-weight:700;
                word-break:break-word;
            ">
                <?= htmlspecialchars($_SESSION['admin_access_error']) ?>
            </div>
            <?php unset($_SESSION['admin_access_error']); ?>
        <?php endif; ?>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding:28px;
            margin-bottom:28px;
        ">
            <h2 style="margin:0 0 16px; color:#f97316; font-size:24px;">Générer un nouvel accès</h2>

            <form method="POST" action="index.php?page=admin_create_access" style="
                display:grid;
                grid-template-columns:minmax(280px, 1fr) auto;
                gap:14px;
                align-items:end;
            ">
                <div>
                    <label style="display:block; font-weight:700; margin-bottom:8px; color:#111827;">Email du candidat</label>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="exemple@email.com"
                        style="
                            width:100%;
                            padding:14px 16px;
                            border:1px solid #d1d5db;
                            border-radius:14px;
                            box-sizing:border-box;
                            font:inherit;
                        "
                    >
                </div>

                <button type="submit" style="
                    background:#f97316;
                    color:white;
                    border:none;
                    padding:14px 24px;
                    border-radius:999px;
                    font-weight:700;
                    cursor:pointer;
                    box-shadow: 0 8px 18px rgba(249, 115, 22, 0.2);
                ">
                    Générer la clé
                </button>
            </form>
        </section>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow:hidden;
        ">
            <div style="padding:24px 28px 12px;">
                <h2 style="margin:0; color:#111827; font-size:28px;">Accès générés</h2>
            </div>

            <?php if (empty($accesses)): ?>
                <div style="padding:28px; color:#4b5563;">Aucun accès généré pour le moment.</div>
            <?php else: ?>
                <div style="overflow:auto;">
                    <div style="
                        min-width:1100px;
                        display:grid;
                        grid-template-columns: 90px 2fr 1.3fr 1.2fr 1.2fr 160px 160px;
                        background:#f97316;
                        color:white;
                        font-weight:800;
                    ">
                        <div style="padding:16px;">ID</div>
                        <div style="padding:16px;">Email</div>
                        <div style="padding:16px;">Clé</div>
                        <div style="padding:16px;">Nom</div>
                        <div style="padding:16px;">Prénom</div>
                        <div style="padding:16px;">Statut</div>
                        <div style="padding:16px;">Action</div>
                    </div>

                    <?php foreach ($accesses as $index => $access): ?>
                        <div style="
                            min-width:1100px;
                            display:grid;
                            grid-template-columns: 90px 2fr 1.3fr 1.2fr 1.2fr 160px 160px;
                            border-bottom:1px solid #e5e7eb;
                            background: <?= $index % 2 === 0 ? '#f9fafb' : 'white' ?>;
                            color:#111827;
                            align-items:center;
                        ">
                            <div style="padding:16px; font-weight:700;"><?= (int)$access['id'] ?></div>

                            <div style="padding:16px; word-break:break-word;">
                                <?= htmlspecialchars($access['email'] ?? '') ?>
                            </div>

                            <div style="padding:16px; font-family:monospace; font-weight:800; color:#f97316;">
                                <?= htmlspecialchars($access['access_key'] ?? '') ?>
                            </div>

                            <div style="padding:16px;">
                                <?= htmlspecialchars($access['nom'] ?? '') ?>
                            </div>

                            <div style="padding:16px;">
                                <?= htmlspecialchars($access['prenom'] ?? '') ?>
                            </div>

                            <div style="padding:16px;">
                                <?php if (!empty($access['is_used'])): ?>
                                    <span style="
                                        display:inline-block;
                                        background:#dcfce7;
                                        color:#166534;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">Utilisée</span>
                                <?php else: ?>
                                    <span style="
                                        display:inline-block;
                                        background:#fef3c7;
                                        color:#92400e;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">En attente</span>
                                <?php endif; ?>
                            </div>

                            <div style="padding:16px;">
                                <form method="POST" action="index.php?page=admin_delete_access" onsubmit="return confirm('Supprimer cet accès ?');" style="margin:0;">
                                    <input type="hidden" name="id" value="<?= (int)$access['id'] ?>">
                                    <button type="submit" style="
                                        background:#dc2626;
                                        color:white;
                                        border:none;
                                        padding:10px 16px;
                                        border-radius:999px;
                                        cursor:pointer;
                                        font-weight:700;
                                    ">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>