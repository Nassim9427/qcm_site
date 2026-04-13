<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px;">
    <h1>Aperçu du quiz</h1>

    <a href="index.php?page=home"
       style="display:inline-block; margin-bottom:20px; color:#2563eb; font-weight:bold;">
        ← Retour à l'accueil
    </a>

    <div style="
        max-width: 900px;
        margin: 0 auto;
        background: white;
        color: black;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    ">
        <div style="margin-bottom: 30px;">
            <h2>Question 1</h2>
            <p>Quelle est la capitale de la France ?</p>
            <ul style="line-height: 1.9;">
                <li>A. Berlin</li>
                <li>B. Madrid</li>
                <li><strong style="color: green;">C. Paris ✅</strong></li>
                <li>D. Rome</li>
            </ul>
        </div>

        <div style="margin-bottom: 30px;">
            <h2>Question 2</h2>
            <p>Combien font 2 + 2 ?</p>
            <ul style="line-height: 1.9;">
                <li>A. 3</li>
                <li><strong style="color: green;">B. 4 ✅</strong></li>
                <li>C. 5</li>
                <li>D. 6</li>
            </ul>
        </div>

        <div style="margin-bottom: 30px;">
            <h2>Question 3</h2>
            <p>Quel mot-clé SQL permet de récupérer des données ?</p>
            <ul style="line-height: 1.9;">
                <li>A. INSERT</li>
                <li>B. UPDATE</li>
                <li>C. DELETE</li>
                <li><strong style="color: green;">D. SELECT ✅</strong></li>
            </ul>
        </div>

        <div style="margin-bottom: 30px;">
            <h2>Question 4</h2>
            <p>Quel type de test vérifie le bon fonctionnement global d’une fonctionnalité côté utilisateur ?</p>
            <ul style="line-height: 1.9;">
                <li>A. Test unitaire</li>
                <li><strong style="color: green;">B. Test fonctionnel ✅</strong></li>
                <li>C. Test de charge</li>
                <li>D. Test réseau</li>
            </ul>
        </div>

        <div style="margin-bottom: 10px;">
            <h2>Question 5</h2>
            <p>Écris uniquement le chiffre <strong>1</strong> dans la zone de texte.</p>
            <div style="
                background:#f9fafb;
                border:1px solid #ddd;
                border-radius:10px;
                padding:14px;
                color:#555;
            ">
                Bonne réponse attendue : <strong style="color: green;">1 ✅</strong>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>