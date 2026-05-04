<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion administrateur</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <style>
        .admin-login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .admin-login-card {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .admin-login-head {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 30px;
        }

        .admin-login-head h1 {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 800;
        }

        .admin-login-head p {
            margin: 0;
            opacity: 0.95;
            font-size: 16px;
        }

        .admin-login-body {
            padding: 28px;
            color: #111827;
        }

        .admin-login-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .admin-login-input {
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

        .admin-login-btn {
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

        .admin-login-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .admin-login-footer-link {
            margin-top: 16px;
            text-align: center;
        }

        .admin-login-footer-link a {
            color: #6b7280;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-login-footer-link a:hover {
            color: #f97316;
        }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="admin-login-head">
                <h1>Connexion admin</h1>
                <p>Réservé aux administrateurs du site.</p>
            </div>

            <div class="admin-login-body">
                <?php if (!empty($_SESSION['admin_login_error'])): ?>
                    <div class="admin-login-error">
                        <?= htmlspecialchars($_SESSION['admin_login_error']) ?>
                    </div>
                    <?php unset($_SESSION['admin_login_error']); ?>
                <?php endif; ?>

                <form method="POST" action="index.php?page=admin_login_submit">
                    <label class="admin-login-label">Email</label>
                    <input type="email" name="email" class="admin-login-input" required placeholder="admin@email.com">

                    <label class="admin-login-label">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="admin-login-input" required placeholder="Votre mot de passe">

                    <button type="submit" class="admin-login-btn">Se connecter</button>
                </form>

                <div class="admin-login-footer-link">
                    <a href="index.php?page=access_login">Retour à la connexion candidat</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>