<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code du test</title>
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

        .test-code-card {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .test-code-header {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .test-code-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .test-code-header p {
            margin: 0;
            opacity: 0.95;
            line-height: 1.5;
        }

        .test-code-body {
            padding: 28px;
        }

        .test-code-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .test-code-input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            margin-bottom: 16px;
            font: inherit;
        }

        .test-code-btn {
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

        .test-code-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .test-code-back {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .test-code-back:hover {
            color: #f97316;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="test-code-card">
        <div class="test-code-header">
            <h1>Code du test</h1>
            <p>
                Vous allez accéder au test :<br>
                <strong><?= htmlspecialchars($selectedTestLabel ?? 'Test ciblé') ?></strong>
            </p>
        </div>

        <div class="test-code-body">
            <?php if (!empty($_SESSION['test_code_error'])): ?>
                <div class="test-code-error">
                    <?= htmlspecialchars($_SESSION['test_code_error']) ?>
                </div>
                <?php unset($_SESSION['test_code_error']); ?>
            <?php endif; ?>

            <form method="POST" action="index.php?page=test_code_submit">
                <input type="hidden" name="test" value="<?= htmlspecialchars($selectedTestMode ?? '') ?>">

                <label class="test-code-label">Code d'accès du test</label>
                <input
                    type="text"
                    name="test_code"
                    class="test-code-input"
                    required
                    placeholder="Ex : sql2026"
                    autocomplete="off"
                >

                <button type="submit" class="test-code-btn">
                    Valider le code
                </button>
            </form>

            <a href="index.php?page=home" class="test-code-back">
                Retour à l'accueil
            </a>
        </div>
    </div>
</body>
</html>