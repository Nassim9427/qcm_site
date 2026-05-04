<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$stats = $stats ?? [];
$ranking = $ranking ?? [];

$total = (int)($stats['total_participants'] ?? 0);
$average = round((float)($stats['average_score'] ?? 0), 2);
$best = (int)($stats['best_score'] ?? 0);
?>

<main style="
    padding: 40px 20px;
    background: linear-gradient(135deg, #4f46e5, #3b82f6);
    min-height: calc(100vh - 120px);
    color: white;
">

    <section style="max-width: 1200px; margin: 0 auto;">

        <!-- HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; flex-wrap:wrap; gap:10px;">
            <h1 style="margin:0; font-size:36px;">Classement & Statistiques</h1>

            <a href="index.php?page=admin_results" style="
                text-decoration:none;
                background: rgba(255,255,255,0.2);
                color:white;
                padding:12px 20px;
                border-radius:999px;
                font-weight:700;
                border:1px solid rgba(255,255,255,0.3);
                backdrop-filter: blur(6px);
                transition:0.2s;
            " onmouseover="this.style.background='rgba(255,255,255,0.35)'" 
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                ⬅ Retour aux résultats
            </a>
        </div>

        <!-- STATS -->
        <div style="
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap:20px;
            margin-bottom:40px;
        ">

            <div style="
                background:white;
                color:black;
                border-radius:18px;
                padding:20px;
                box-shadow:0 10px 25px rgba(0,0,0,0.15);
            ">
                <div style="color:#f97316; font-weight:700;">Total des tests</div>
                <div style="font-size:32px; font-weight:800;"><?= $total ?></div>
            </div>

            <div style="
                background:white;
                color:black;
                border-radius:18px;
                padding:20px;
                box-shadow:0 10px 25px rgba(0,0,0,0.15);
            ">
                <div style="color:#f97316; font-weight:700;">Score moyen</div>
                <div style="font-size:32px; font-weight:800;"><?= $average ?></div>
            </div>

            <div style="
                background:white;
                color:black;
                border-radius:18px;
                padding:20px;
                box-shadow:0 10px 25px rgba(0,0,0,0.15);
            ">
                <div style="color:#f97316; font-weight:700;">Meilleur score</div>
                <div style="font-size:32px; font-weight:800;"><?= $best ?></div>
            </div>

        </div>

        <!-- TABLE -->
        <h2 style="margin-bottom:15px;">Classement des candidats</h2>

        <div style="
            background:white;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 25px rgba(0,0,0,0.15);
        ">

            <!-- HEADER -->
            <div style="
                display:grid;
                grid-template-columns: 80px 1fr 1fr 2fr 120px 100px 200px;
                background:#f97316;
                color:white;
                font-weight:800;
                padding:14px;
            ">
                <div>Rang</div>
                <div>Nom</div>
                <div>Prénom</div>
                <div>Email</div>
                <div>Score</div>
                <div>Total</div>
                <div>Date</div>
            </div>

            <!-- ROWS -->
            <?php foreach ($ranking as $index => $candidate): ?>
                <div style="
                    display:grid;
                    grid-template-columns: 80px 1fr 1fr 2fr 120px 100px 200px;
                    padding:14px;
                    border-bottom:1px solid #e5e7eb;
                    align-items:center;
                    color:black;
                    background: <?= $index % 2 === 0 ? '#f9fafb' : 'white' ?>;
                ">

                    <div style="font-weight:800;"><?= $index + 1 ?></div>

                    <div><?= htmlspecialchars($candidate['nom'] ?? '') ?></div>

                    <div><?= htmlspecialchars($candidate['prenom'] ?? '') ?></div>

                    <div style="font-size:14px; color:#374151;">
                        <?= htmlspecialchars($candidate['email'] ?? 'Non renseigné') ?>
                    </div>

                    <div style="font-weight:800; color:#f97316;">
                        <?= (int)$candidate['score'] ?>
                    </div>

                    <div><?= (int)$candidate['total_questions'] ?></div>

                    <div style="font-size:14px; color:#6b7280;">
                        <?= htmlspecialchars($candidate['created_at'] ?? '') ?>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>

    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>