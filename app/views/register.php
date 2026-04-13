<?php require __DIR__ . '/partials/header.php'; ?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Inscription</h1>
        <p class="auth-subtitle">Créez votre compte pour accéder aux tests QCM.</p>

        <?php if (!empty($errors)): ?>
            <div class="auth-errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="?page=register" method="POST" class="auth-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="entreprise">Entreprise</label>
                <input type="text" id="entreprise" name="entreprise" value="<?= htmlspecialchars($old['entreprise'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="poste">Poste</label>
                <input type="text" id="poste" name="poste" value="<?= htmlspecialchars($old['poste'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <div class="form-group">
                <label for="confirmation_mot_de_passe">Confirmer le mot de passe</label>
                <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
            </div>

            <button type="submit" class="auth-button">S’inscrire</button>
        </form>

        <p class="auth-link">
            Déjà inscrit ?
            <a href="?page=login">Se connecter</a>
        </p>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>