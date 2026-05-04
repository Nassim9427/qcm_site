<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion par clé</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
        }

        .access-card {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .access-header {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 30px;
        }

        .access-header h1 {
            margin: 0 0 8px;
            font-size: 32px;
        }

        .access-header p {
            margin: 0;
            opacity: 0.95;
        }

        .access-body {
            padding: 28px;
        }

        .access-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .access-input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            margin-bottom: 16px;
            font: inherit;
        }

        .access-btn {
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

        .access-note {
            margin-top: 16px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
        }

        .access-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="access-card">
        <div class="access-header">
            <h1>Connexion candidat</h1>
            <p>Entrez votre email et votre clé d'accès reçue.</p>
        </div>

        <div class="access-body">
            <?php if (!empty($_SESSION['access_error'])): ?>
                <div class="access-error">
                    <?= htmlspecialchars($_SESSION['access_error']) ?>
                </div>
                <?php unset($_SESSION['access_error']); ?>
            <?php endif; ?>

            <form method="POST" action="index.php?page=access_login_submit">
                <label class="access-label">Email</label>
                <input type="email" name="email" class="access-input" required placeholder="exemple@email.com">

                <label class="access-label">Clé d'accès</label>
                <input type="text" name="access_key" class="access-input" required placeholder="Ex : A1B2C3D4E5F6">

                <button type="submit" class="access-btn">Continuer</button>
            </form>

            <div class="access-note">
                La clé d'accès est à usage unique. Une fois utilisée, elle ne peut plus servir à se reconnecter.
            </div>
        </div>
    </div>
</body>
</html>