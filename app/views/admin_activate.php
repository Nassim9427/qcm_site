<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation compte admin</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <style>
        .admin-activate-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .admin-activate-card {
            width: 100%;
            max-width: 560px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .admin-activate-head {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 30px;
        }

        .admin-activate-head h1 {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 800;
        }

        .admin-activate-head p {
            margin: 0;
            opacity: 0.95;
            font-size: 16px;
        }

        .admin-activate-body {
            padding: 28px;
            color: #111827;
        }

        .admin-activate-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .admin-activate-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            margin-bottom: 16px;
            box-sizing: border-box;
            font: inherit;
            color: #111827;
            background: white;
        }

        .admin-activate-btn {
            width: 100%;
            border: none;
            background: #f97316;
            color: white;
            padding: 14px 18px;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(249, 115, 22, 0.2);
        }

        .admin-activate-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .admin-activate-footer-link {
            margin-top: 16px;
            text-align: center;
        }

        .admin-activate-footer-link a {
            color: #6b7280;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-activate-footer-link a:hover {
            color: #f97316;
        }
    </style>
</head>
<body>
    <div class="admin-activate-wrapper">
        <div class="admin-activate-card">
            <div class="admin-activate-head">
                <h1>Activer le compte admin</h1>
                <p>Complétez vos informations pour finaliser votre compte administrateur.</p>
            </div>

            <div class="admin-activate-body">
                <?php if (!empty($_SESSION['admin_activate_error'])): ?>
                    <div class="admin-activate-error">
                        <?= htmlspecialchars($_SESSION['admin_activate_error']) ?>
                    </div>
                    <?php unset($_SESSION['admin_activate_error']); ?>
                <?php endif; ?>

                <form method="POST" action="index.php?page=admin_activate_submit">
                    <label class="admin-activate-label">Email</label>
                    <input type="email" name="email" class="admin-activate-input" required placeholder="admin@email.com">

                    <label class="admin-activate-label">Token d'invitation</label>
                    <input type="text" name="invitation_token" class="admin-activate-input" required placeholder="Ex : A1B2C3D4E5F6G7H8">

                    <label class="admin-activate-label">Nom</label>
                    <input type="text" name="nom" class="admin-activate-input" required placeholder="Votre nom">

                    <label class="admin-activate-label">Prénom</label>
                    <input type="text" name="prenom" class="admin-activate-input" required placeholder="Votre prénom">

                    <label class="admin-activate-label">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="admin-activate-input" required placeholder="Au moins 8 caractères">

                    <label class="admin-activate-label">Confirmer le mot de passe</label>
                    <input type="password" name="confirmation_mot_de_passe" class="admin-activate-input" required placeholder="Confirmez le mot de passe">

                    <button type="submit" class="admin-activate-btn">Activer mon compte admin</button>
                </form>

                <div class="admin-activate-footer-link">
                    <a href="index.php?page=access_login">Retour à la connexion candidat</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>