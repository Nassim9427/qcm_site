<?php require __DIR__ . '/partials/header.php'; ?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Connexion</h1>
        <p class="auth-subtitle">Connectez-vous pour accéder au QCM.</p>

        <?php if (!empty($errors)): ?>
            <div class="auth-errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="?page=login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <button type="submit" class="auth-button">Se connecter</button>
        </form>

        <p class="auth-link">
            Pas encore de compte ?
            <a href="?page=register">S’inscrire</a>
        </p>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>