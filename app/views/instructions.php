<?php require __DIR__ . '/partials/header.php'; ?>

<main style="padding: 40px 20px; background: #f5f5f5; min-height: calc(100vh - 120px);">
    <section style="
        max-width: 900px;
        margin: 0 auto;
        background: white;
        color: black;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    ">
        <div style="
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 32px 30px;
        ">
            <h1 style="margin: 0 0 10px; font-size: 34px;">Instructions du quiz</h1>
            <p style="margin: 0; font-size: 16px; opacity: 0.95;">
                Lis bien toutes les consignes avant de commencer.
            </p>
        </div>

        <div style="padding: 30px;">
            <div style="
                background: #fff7ed;
                border: 1px solid #fdba74;
                color: #9a3412;
                border-radius: 18px;
                padding: 22px;
                margin-bottom: 26px;
            ">
                <h2 style="margin-top: 0; margin-bottom: 12px; color: #c2410c;">⚠️ Attention</h2>
                <p style="margin: 0 0 12px; font-weight: 700;">
                    Ce quiz est en mode examen.
                </p>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                    <li>Le temps est limité à <strong>10 minutes</strong>.</li>
                    <li>Le chronomètre continue même si la page est actualisée.</li>
                    <li>Revenir en arrière ou essayer de contourner le quiz n’accorde pas de temps supplémentaire.</li>
                    <li>Si vous quittez l’onglet ou la fenêtre du quiz, le test peut être envoyé automatiquement.</li>
                    <li>Une fois le quiz commencé, répondez sans quitter la page.</li>
                </ul>
            </div>

            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 18px;
                margin-bottom: 28px;
            ">
                <div style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 20px;
                ">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #f97316;">⏱ Durée</h3>
                    <p style="margin: 0; color: #374151;">Vous disposez de 10 minutes pour répondre à toutes les questions.</p>
                </div>

                <div style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 20px;
                ">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #f97316;">📝 Réponses</h3>
                    <p style="margin: 0; color: #374151;">Certaines questions sont des QCM, d’autres demandent une réponse écrite.</p>
                </div>

                <div style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 20px;
                ">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #f97316;">🖼 Images</h3>
                    <p style="margin: 0; color: #374151;">Quand une question contient une image, vous pouvez cliquer dessus pour l’agrandir.</p>
                </div>
            </div>

            <div style="margin-bottom: 28px;">
                <h2 style="color: #111827; margin-bottom: 14px;">Consignes</h2>
                <ul style="padding-left: 20px; line-height: 1.9; color: #374151; margin: 0;">
                    <li>Lisez chaque question attentivement avant de répondre.</li>
                    <li>Pour les QCM, une seule réponse est attendue.</li>
                    <li>Pour les questions texte, respectez bien la consigne demandée.</li>
                    <li>Lorsque le temps arrive à zéro, le quiz est envoyé automatiquement.</li>
                    <li>Vérifiez vos réponses avant de valider.</li>
                </ul>
            </div>

            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
                border-top: 1px solid #e5e7eb;
                padding-top: 24px;
            ">
                <a href="index.php?page=home" style="
                    display: inline-block;
                    text-decoration: none;
                    background: #e5e7eb;
                    color: #374151;
                    padding: 14px 22px;
                    border-radius: 999px;
                    font-weight: 700;
                ">
                    Retour
                </a>

                <a href="index.php?page=quiz" style="
                    display: inline-block;
                    text-decoration: none;
                    background: #f97316;
                    color: white;
                    padding: 14px 26px;
                    border-radius: 999px;
                    font-weight: 700;
                    box-shadow: 0 8px 18px rgba(249, 115, 22, 0.25);
                ">
                    Commencer le quiz
                </a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>