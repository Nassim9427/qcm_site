<?php require __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/../../config/app.php'; ?>

<main style="padding: 40px 20px; background: #f5f5f5; min-height: calc(100vh - 120px); overflow-x:hidden;">
    <section style="max-width: 1280px; margin: 0 auto; width:100%;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:24px;">
            <div>
                <h1 style="margin:0 0 8px; font-size:42px; color:#111827;">Gestion des utilisateurs</h1>
                <p style="margin:0; color:#4b5563;">Gérez les comptes, les rôles et les invitations administrateur.</p>
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

        <?php if (!empty($_SESSION['admin_users_success'])): ?>
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
                <?= htmlspecialchars($_SESSION['admin_users_success']) ?>
            </div>
            <?php unset($_SESSION['admin_users_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['admin_users_error'])): ?>
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
                <?= htmlspecialchars($_SESSION['admin_users_error']) ?>
            </div>
            <?php unset($_SESSION['admin_users_error']); ?>
        <?php endif; ?>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding:24px;
            margin-bottom:28px;
        ">
            <form method="GET" action="index.php" style="
                display:grid;
                grid-template-columns: minmax(260px, 1fr) 220px auto;
                gap:14px;
                align-items:end;
            ">
                <input type="hidden" name="page" value="admin_users">

                <div>
                    <label style="display:block; font-weight:700; margin-bottom:8px; color:#111827;">Recherche</label>
                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($search ?? '') ?>"
                        placeholder="Nom, prénom ou email"
                        style="
                            width:100%;
                            padding:14px 16px;
                            border:1px solid #d1d5db;
                            border-radius:14px;
                            box-sizing:border-box;
                            font:inherit;
                            color:#111827;
                            background:white;
                        "
                    >
                </div>

                <div>
                    <label style="display:block; font-weight:700; margin-bottom:8px; color:#111827;">Rôle</label>
                    <select
                        name="role"
                        style="
                            width:100%;
                            padding:14px 16px;
                            border:1px solid #d1d5db;
                            border-radius:14px;
                            box-sizing:border-box;
                            font:inherit;
                            color:#111827;
                            background:white;
                        "
                    >
                        <option value="" <?= ($role ?? '') === '' ? 'selected' : '' ?>>Tous</option>
                        <option value="user" <?= ($role ?? '') === 'user' ? 'selected' : '' ?>>Utilisateurs</option>
                        <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                    </select>
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
                    Filtrer
                </button>
            </form>
        </section>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow:hidden;
            margin-bottom:28px;
        ">
            <div style="padding:24px 28px 12px;">
                <h2 style="margin:0; color:#111827; font-size:28px;">Utilisateurs inscrits</h2>
            </div>

            <?php if (empty($users)): ?>
                <div style="padding:28px; color:#4b5563;">Aucun utilisateur trouvé.</div>
            <?php else: ?>
                <div style="padding: 0 18px 18px;">
                    <div style="
                        display:grid;
                        grid-template-columns: 70px 1fr 1fr 2fr 130px 150px 140px 140px;
                        background:#f97316;
                        color:white;
                        font-weight:800;
                        border-radius:16px 16px 0 0;
                        overflow:hidden;
                    ">
                        <div style="padding:16px;">ID</div>
                        <div style="padding:16px;">Nom</div>
                        <div style="padding:16px;">Prénom</div>
                        <div style="padding:16px;">Email</div>
                        <div style="padding:16px;">Rôle</div>
                        <div style="padding:16px;">Quiz</div>
                        <div style="padding:16px;">Résultat</div>
                        <div style="padding:16px;">Détails</div>
                    </div>

                    <?php foreach ($users as $index => $user): ?>
                        <?php
                            $panelId = 'user-panel-' . (int)$user['id'];
                            $rightsId = 'rights-panel-' . (int)$user['id'];
                        ?>
                        <div style="
                            display:grid;
                            grid-template-columns: 70px 1fr 1fr 2fr 130px 150px 140px 140px;
                            background: <?= $index % 2 === 0 ? '#f9fafb' : 'white' ?>;
                            color:#111827;
                            border-left:1px solid #e5e7eb;
                            border-right:1px solid #e5e7eb;
                            border-bottom:1px solid #e5e7eb;
                            align-items:start;
                        ">
                            <div style="padding:18px; font-weight:700;"><?= (int)$user['id'] ?></div>

                            <div style="padding:18px; word-break:break-word;">
                                <?= htmlspecialchars($user['nom'] ?? '') ?>
                            </div>

                            <div style="padding:18px; word-break:break-word;">
                                <?= htmlspecialchars($user['prenom'] ?? '') ?>
                            </div>

                            <div style="padding:18px; word-break:break-word; line-height:1.45;">
                                <?= htmlspecialchars($user['email'] ?? '') ?>
                            </div>

                            <div style="padding:18px;">
                                <?php if (!empty($user['is_admin'])): ?>
                                    <span style="
                                        display:inline-block;
                                        background:#dbeafe;
                                        color:#1d4ed8;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">Admin</span>
                                <?php else: ?>
                                    <span style="
                                        display:inline-block;
                                        background:#f3f4f6;
                                        color:#374151;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">Utilisateur</span>
                                <?php endif; ?>
                            </div>

                            <div style="padding:18px;">
                                <?php if (!empty($user['quiz_result_id'])): ?>
                                    <span style="
                                        display:inline-block;
                                        background:#dcfce7;
                                        color:#166534;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">
                                        Terminé
                                    </span>
                                <?php else: ?>
                                    <span style="
                                        display:inline-block;
                                        background:#fef3c7;
                                        color:#92400e;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        font-weight:700;
                                        font-size:13px;
                                    ">
                                        Pas encore
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div style="padding:18px;">
                                <?php if (!empty($user['quiz_result_id'])): ?>
                                    <a href="index.php?page=admin_result_details&result_id=<?= (int)$user['quiz_result_id'] ?>" style="
                                        display:block;
                                        text-decoration:none;
                                        text-align:center;
                                        background:#f3f4f6;
                                        color:#374151;
                                        padding:10px 14px;
                                        border-radius:999px;
                                        font-weight:700;
                                    ">
                                        Voir résultat
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div style="padding:18px;">
                                <button
                                    type="button"
                                    class="js-toggle-panel"
                                    data-target="<?= htmlspecialchars($panelId) ?>"
                                    style="
                                        width:100%;
                                        background:#fff7ed;
                                        color:#c2410c;
                                        border:1px solid #fdba74;
                                        padding:10px 14px;
                                        border-radius:999px;
                                        cursor:pointer;
                                        font-weight:700;
                                    "
                                >
                                    Plus d'infos
                                </button>
                            </div>
                        </div>

                        <div
                            id="<?= htmlspecialchars($panelId) ?>"
                            class="js-user-panel"
                            style="
                                display:none;
                                background:#fff;
                                border-left:1px solid #e5e7eb;
                                border-right:1px solid #e5e7eb;
                                border-bottom:1px solid #e5e7eb;
                                padding:22px 24px;
                            "
                        >
                            <div style="
                                display:grid;
                                grid-template-columns: 1.4fr 1fr;
                                gap:22px;
                                align-items:start;
                            ">
                                <div style="
                                    background:#f9fafb;
                                    border:1px solid #e5e7eb;
                                    border-radius:16px;
                                    padding:18px;
                                ">
                                    <h3 style="margin:0 0 14px; color:#111827; font-size:20px;">Informations</h3>

                                    <div style="display:grid; grid-template-columns: 150px 1fr; gap:10px 14px; color:#374151; line-height:1.5;">
                                        <div style="font-weight:700;">Email</div>
                                        <div style="word-break:break-word;"><?= htmlspecialchars($user['email'] ?? '') ?></div>

                                        <div style="font-weight:700;">Rôle</div>
                                        <div><?= !empty($user['is_admin']) ? 'Administrateur' : 'Utilisateur' ?></div>

                                        <div style="font-weight:700;">Date création</div>
                                        <div><?= htmlspecialchars($user['created_at'] ?? 'Non disponible') ?></div>

                                        <div style="font-weight:700;">Quiz</div>
                                        <div>
                                            <?php if (!empty($user['quiz_result_id'])): ?>
                                                Terminé — Score : <?= (int)$user['score'] ?>/<?= (int)$user['total_questions'] ?>
                                            <?php else: ?>
                                                Pas encore passé
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div style="
                                    background:#fff;
                                    border:1px solid #e5e7eb;
                                    border-radius:16px;
                                    padding:18px;
                                ">
                                    <div style="display:flex; flex-direction:column; gap:12px;">
                                        <button
                                            type="button"
                                            class="js-toggle-rights"
                                            data-target="<?= htmlspecialchars($rightsId) ?>"
                                            style="
                                                width:100%;
                                                background:#6b7280;
                                                color:white;
                                                border:none;
                                                padding:12px 16px;
                                                border-radius:999px;
                                                cursor:pointer;
                                                font-weight:700;
                                            "
                                        >
                                            Gérer les droits
                                        </button>

                                        <div
                                            id="<?= htmlspecialchars($rightsId) ?>"
                                            class="js-rights-panel"
                                            style="display:none;"
                                        >
                                            <?php if (empty($user['is_admin'])): ?>
                                                <form
                                                    method="POST"
                                                    action="index.php?page=admin_users_promote"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir nommer administrateur cet utilisateur ?');"
                                                    style="margin:0 0 12px;"
                                                >
                                                    <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                    <button type="submit" style="
                                                        width:100%;
                                                        background:#2563eb;
                                                        color:white;
                                                        border:none;
                                                        padding:12px 16px;
                                                        border-radius:999px;
                                                        cursor:pointer;
                                                        font-weight:700;
                                                    ">
                                                        Nommer administrateur
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                                    <form
                                                        method="POST"
                                                        action="index.php?page=admin_users_demote"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir retirer les droits administrateur ?');"
                                                        style="margin:0 0 12px;"
                                                    >
                                                        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                        <button type="submit" style="
                                                            width:100%;
                                                            background:#4b5563;
                                                            color:white;
                                                            border:none;
                                                            padding:12px 16px;
                                                            border-radius:999px;
                                                            cursor:pointer;
                                                            font-weight:700;
                                                        ">
                                                            Retirer les droits admin
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                            <form
                                                method="POST"
                                                action="index.php?page=admin_users_delete"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"
                                                style="margin:0;"
                                            >
                                                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                <button type="submit" style="
                                                    width:100%;
                                                    background:#dc2626;
                                                    color:white;
                                                    border:none;
                                                    padding:12px 16px;
                                                    border-radius:999px;
                                                    cursor:pointer;
                                                    font-weight:700;
                                                ">
                                                    Supprimer l'utilisateur
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="height:1px; background:#e5e7eb; border-radius:0 0 16px 16px;"></div>
                </div>
            <?php endif; ?>
        </section>

        <section style="
            background:white;
            border-radius:22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding:28px;
            margin-bottom:28px;
        ">
            <h2 style="margin:0 0 16px; color:#f97316; font-size:24px;">Inviter un administrateur</h2>

            <form method="POST" action="index.php?page=admin_users_invite_admin" style="
                display:grid;
                grid-template-columns:minmax(280px, 1fr) auto;
                gap:14px;
                align-items:end;
            ">
                <div>
                    <label style="display:block; font-weight:700; margin-bottom:8px; color:#111827;">Email du futur admin</label>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="admin@email.com"
                        style="
                            width:100%;
                            padding:14px 16px;
                            border:1px solid #d1d5db;
                            border-radius:14px;
                            box-sizing:border-box;
                            font:inherit;
                            color:#111827;
                            background:white;
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
                    Générer l'invitation
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
                <h2 style="margin:0; color:#111827; font-size:28px;">Invitations admin</h2>
            </div>

            <?php if (empty($invitations)): ?>
                <div style="padding:28px; color:#4b5563;">Aucune invitation admin pour le moment.</div>
            <?php else: ?>
                <div style="padding: 0 18px 18px;">
                    <div style="
                        display:grid;
                        grid-template-columns: 70px 1.3fr 1fr 1.5fr 120px 180px;
                        background:#f97316;
                        color:white;
                        font-weight:800;
                        border-radius:16px 16px 0 0;
                        overflow:hidden;
                    ">
                        <div style="padding:16px;">ID</div>
                        <div style="padding:16px;">Email</div>
                        <div style="padding:16px;">Token</div>
                        <div style="padding:16px;">Lien</div>
                        <div style="padding:16px;">Statut</div>
                        <div style="padding:16px;">Actions</div>
                    </div>

                    <?php foreach ($invitations as $index => $invitation): ?>
                        <?php $activationLink = APP_URL . '/index.php?page=admin_activate'; ?>
                        <div style="
                            display:grid;
                            grid-template-columns: 70px 1.3fr 1fr 1.5fr 120px 180px;
                            background: <?= $index % 2 === 0 ? '#f9fafb' : 'white' ?>;
                            color:#111827;
                            border-left:1px solid #e5e7eb;
                            border-right:1px solid #e5e7eb;
                            border-bottom:1px solid #e5e7eb;
                            align-items:start;
                        ">
                            <div style="padding:18px; font-weight:700;"><?= (int)$invitation['id'] ?></div>

                            <div style="padding:18px; word-break:break-word;">
                                <?= htmlspecialchars($invitation['email'] ?? '') ?>
                            </div>

                            <div style="padding:18px;">
                                <div style="font-family:monospace; font-weight:800; color:#f97316; margin-bottom:8px; word-break:break-all;">
                                    <?= htmlspecialchars($invitation['invitation_token'] ?? '') ?>
                                </div>

                                <button
                                    type="button"
                                    class="js-copy-token"
                                    data-token="<?= htmlspecialchars($invitation['invitation_token'] ?? '') ?>"
                                    style="
                                        background:#e5e7eb;
                                        color:#374151;
                                        border:none;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        cursor:pointer;
                                        font-weight:700;
                                        font-size:13px;
                                        width:100%;
                                    "
                                >
                                    Copier token
                                </button>
                            </div>

                            <div style="padding:18px;">
                                <div style="
                                    font-size:13px;
                                    color:#4b5563;
                                    word-break:break-all;
                                    margin-bottom:8px;
                                    line-height:1.5;
                                ">
                                    <?= htmlspecialchars($activationLink) ?>
                                </div>

                                <button
                                    type="button"
                                    class="js-copy-link"
                                    data-link="<?= htmlspecialchars($activationLink) ?>"
                                    style="
                                        background:#dbeafe;
                                        color:#1d4ed8;
                                        border:none;
                                        padding:8px 12px;
                                        border-radius:999px;
                                        cursor:pointer;
                                        font-weight:700;
                                        font-size:13px;
                                        width:100%;
                                    "
                                >
                                    Copier lien
                                </button>
                            </div>

                            <div style="padding:18px;">
                                <?php if (!empty($invitation['is_used'])): ?>
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

                            <div style="padding:18px;">
                                <div style="display:flex; flex-direction:column; gap:10px;">
                                    <?php if (empty($invitation['is_used'])): ?>
                                        <form method="POST" action="index.php?page=admin_users_resend_invitation" style="margin:0;">
                                            <input type="hidden" name="id" value="<?= (int)$invitation['id'] ?>">
                                            <button type="submit" style="
                                                width:100%;
                                                background:#2563eb;
                                                color:white;
                                                border:none;
                                                padding:10px 14px;
                                                border-radius:999px;
                                                cursor:pointer;
                                                font-weight:700;
                                            ">
                                                Renvoyer
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" action="index.php?page=admin_users_delete_invitation" onsubmit="return confirm('Supprimer cette invitation admin ?');" style="margin:0;">
                                        <input type="hidden" name="id" value="<?= (int)$invitation['id'] ?>">
                                        <button type="submit" style="
                                            width:100%;
                                            background:#dc2626;
                                            color:white;
                                            border:none;
                                            padding:10px 14px;
                                            border-radius:999px;
                                            cursor:pointer;
                                            font-weight:700;
                                        ">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="height:1px; background:#e5e7eb; border-radius:0 0 16px 16px;"></div>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyTokenButtons = document.querySelectorAll('.js-copy-token');
        const copyLinkButtons = document.querySelectorAll('.js-copy-link');
        const toggleButtons = document.querySelectorAll('.js-toggle-panel');
        const rightsButtons = document.querySelectorAll('.js-toggle-rights');

        copyTokenButtons.forEach(function (button) {
            button.addEventListener('click', async function () {
                const token = button.getAttribute('data-token') || '';

                try {
                    await navigator.clipboard.writeText(token);
                    const originalText = button.textContent;
                    button.textContent = 'Copié';
                    setTimeout(function () {
                        button.textContent = originalText;
                    }, 1200);
                } catch (error) {
                    alert('Impossible de copier automatiquement le token.');
                }
            });
        });

        copyLinkButtons.forEach(function (button) {
            button.addEventListener('click', async function () {
                const link = button.getAttribute('data-link') || '';

                try {
                    await navigator.clipboard.writeText(link);
                    const originalText = button.textContent;
                    button.textContent = 'Lien copié';
                    setTimeout(function () {
                        button.textContent = originalText;
                    }, 1200);
                } catch (error) {
                    alert('Impossible de copier automatiquement le lien.');
                }
            });
        });

        toggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-target');
                const panel = document.getElementById(targetId);

                if (!panel) {
                    return;
                }

                const isOpen = panel.style.display === 'block';

                document.querySelectorAll('.js-user-panel').forEach(function (otherPanel) {
                    otherPanel.style.display = 'none';
                });

                document.querySelectorAll('.js-toggle-panel').forEach(function (otherButton) {
                    otherButton.textContent = "Plus d'infos";
                });

                if (!isOpen) {
                    panel.style.display = 'block';
                    button.textContent = 'Fermer';
                }
            });
        });

        rightsButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-target');
                const panel = document.getElementById(targetId);

                if (!panel) {
                    return;
                }

                const isOpen = panel.style.display === 'block';

                document.querySelectorAll('.js-rights-panel').forEach(function (otherPanel) {
                    if (otherPanel !== panel) {
                        otherPanel.style.display = 'none';
                    }
                });

                panel.style.display = isOpen ? 'none' : 'block';
            });
        });
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>