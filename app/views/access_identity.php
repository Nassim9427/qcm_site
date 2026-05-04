<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identité candidat</title>
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

        .identity-card {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .identity-header {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 30px;
        }

        .identity-header h1 {
            margin: 0 0 8px;
            font-size: 32px;
        }

        .identity-header p {
            margin: 0;
            opacity: 0.95;
        }

        .identity-body {
            padding: 28px;
        }

        .identity-info {
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-weight: 700;
        }

        .identity-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .identity-input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            margin-bottom: 16px;
            font: inherit;
        }

        .identity-btn {
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

        .identity-error {
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
    <div class="identity-card">
        <div class="identity-header">
            <h1>Identité du candidat</h1>
            <p>Complétez votre nom et votre prénom pour finaliser l'accès.</p>
        </div>

        <div class="identity-body">
            <?php if (!empty($_SESSION['access_identity_error'])): ?>
                <div class="identity-error">
                    <?= htmlspecialchars($_SESSION['access_identity_error']) ?>
                </div>
                <?php unset($_SESSION['access_identity_error']); ?>
            <?php endif; ?>

            <div class="identity-info">
                Email autorisé : <?= htmlspecialchars($access['email'] ?? '') ?>
            </div>

            <form method="POST" action="index.php?page=access_identity_submit">
                <label class="identity-label">Nom</label>
                <input type="text" name="nom" class="identity-input" required placeholder="Votre nom">

                <label class="identity-label">Prénom</label>
                <input type="text" name="prenom" class="identity-input" required placeholder="Votre prénom">

                <button type="submit" class="identity-btn">Valider et accéder au quiz</button>
            </form>
        </div>
    </div>
</body>
</html>