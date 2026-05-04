<?php require __DIR__ . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizQuestion.php';
require_once __DIR__ . '/../models/QuizResult.php';

$database = new Database();
$pdo = $database->getConnection();

$questionModel = new QuizQuestion($pdo);
$resultModel = new QuizResult($pdo);

$userResult = $result ?? null;

if (!$userResult && isset($_SESSION['user_id'])) {
    $userResult = $resultModel->getResultByUserId($_SESSION['user_id']);
}

$answers = [];
$corrections = [];

if (!empty($userResult['id'])) {
    $answers = $resultModel->getAnswersByResultId($userResult['id']);

    foreach ($answers as $index => $answer) {
        $questionId = 0;

        if (preg_match('/question_(\d+)/', (string)$answer['question_key'], $matches)) {
            $questionId = (int)$matches[1];
        }

        if ($questionId <= 0) {
            continue;
        }

        $question = $questionModel->getQuestionById($questionId);

        if (!$question) {
            continue;
        }

        $correction = [
            'number' => $index + 1,
            'question' => $question,
            'answer' => $answer,
            'qcm_rows' => [],
        ];

        if (($question['question_type'] ?? '') === 'qcm') {
            $correctKey = (string)($question['correct_answer'] ?? '');
            $givenKey = '';

            if (!empty($answer['answer_given']) && preg_match('/^([a-z])/i', (string)$answer['answer_given'], $match)) {
                $givenKey = strtolower($match[1]);
            }

            foreach (($question['options'] ?? []) as $option) {
                $optionKey = strtolower((string)$option['key']);

                $correction['qcm_rows'][] = [
                    'key' => strtoupper($optionKey),
                    'text' => $option['text'],
                    'expected' => $optionKey === $correctKey,
                    'selected' => $optionKey === $givenKey,
                    'discordant' => ($optionKey === $correctKey && $optionKey !== $givenKey)
                        || ($optionKey !== $correctKey && $optionKey === $givenKey),
                ];
            }
        }

        $corrections[] = $correction;
    }
}

$score = isset($userResult['score']) ? (int)$userResult['score'] : 0;
$totalQuestions = isset($userResult['total_questions']) ? (int)$userResult['total_questions'] : 0;
$percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;
$isAdmin = isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;

if ($percentage >= 80) {
    $badgeText = "Excellent";
    $badgeBg = "#dcfce7";
    $badgeColor = "#166534";
} elseif ($percentage >= 50) {
    $badgeText = "Bon résultat";
    $badgeBg = "#fef3c7";
    $badgeColor = "#92400e";
} else {
    $badgeText = "À améliorer";
    $badgeBg = "#fee2e2";
    $badgeColor = "#991b1b";
}
?>

<main style="padding: 40px 20px; background: #f5f5f5; min-height: calc(100vh - 120px);">
    <section style="
        max-width: 1100px;
        margin: 0 auto 30px;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    ">
        <div style="
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
            padding: 34px 30px;
            text-align: center;
        ">
            <div style="
                width: 84px;
                height: 84px;
                margin: 0 auto 18px;
                border-radius: 999px;
                background: rgba(255,255,255,0.18);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 42px;
            ">
                📋
            </div>

            <h1 style="margin: 0 0 10px; font-size: 34px;">Ma correction détaillée</h1>
            <p style="margin: 0; font-size: 16px; opacity: 0.96;">
                Retrouvez le détail de vos réponses question par question.
            </p>
        </div>

        <div style="padding: 30px;">
            <div style="text-align:center; margin-bottom: 26px;">
                <span style="
                    display: inline-block;
                    background: <?= $badgeBg ?>;
                    color: <?= $badgeColor ?>;
                    padding: 10px 18px;
                    border-radius: 999px;
                    font-weight: 700;
                    font-size: 14px;
                ">
                    <?= htmlspecialchars($badgeText) ?>
                </span>
            </div>

            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 18px;
                margin-bottom: 20px;
            ">
                <div style="background:#fff7ed; border:1px solid #fdba74; border-radius:18px; padding:22px; text-align:center;">
                    <div style="font-size:14px; color:#9a3412; font-weight:700; margin-bottom:8px;">Score</div>
                    <div style="font-size:34px; font-weight:800; color:#ea580c;"><?= $score ?>/<?= $totalQuestions ?></div>
                </div>

                <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:18px; padding:22px; text-align:center;">
                    <div style="font-size:14px; color:#6b7280; font-weight:700; margin-bottom:8px;">Bonnes réponses</div>
                    <div style="font-size:34px; font-weight:800; color:#111827;"><?= $score ?></div>
                </div>

                <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:18px; padding:22px; text-align:center;">
                    <div style="font-size:14px; color:#6b7280; font-weight:700; margin-bottom:8px;">Pourcentage</div>
                    <div style="font-size:34px; font-weight:800; color:#111827;"><?= $percentage ?>%</div>
                </div>
            </div>
        </div>
    </section>

    <?php if (empty($corrections)): ?>
        <section style="max-width:1100px; margin:0 auto;">
            <div style="
                background:white;
                border-radius:20px;
                padding:24px;
                box-shadow: 0 10px 26px rgba(0, 0, 0, 0.07);
            ">
                Aucun détail de correction disponible.
            </div>
        </section>
    <?php endif; ?>

    <?php foreach ($corrections as $item): ?>
        <?php
            $question = $item['question'];
            $answer = $item['answer'];
            $isCorrect = !empty($answer['is_correct']);
            $questionTypeLabel = ($question['question_type'] === 'qcm') ? 'QCM' : 'Question texte';
            $statusText = $isCorrect ? 'Réponse correcte' : 'Réponse incorrecte';
            $statusBg = $isCorrect ? '#dbeadf' : '#efe5c8';
            $statusColor = $isCorrect ? '#2f7d32' : '#c26b00';
        ?>

        <section style="
            max-width: 1100px;
            margin: 0 auto 26px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.07);
        ">
            <div style="
                background: #f97316;
                color: white;
                padding: 18px 22px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
                font-weight: 800;
                font-size: 18px;
            ">
                <div>❔ Question <?= (int)$item['number'] ?></div>
                <div style="font-size:16px; font-weight:700;"><?= htmlspecialchars($questionTypeLabel) ?></div>
            </div>

            <div style="padding: 22px;">
                <div style="font-size: 26px; color: <?= $statusColor ?>; background: <?= $statusBg ?>; border: 1px solid <?= $isCorrect ? '#b8d6bf' : '#e2c98b' ?>; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; font-weight: 700;">
                    <?= htmlspecialchars($statusText) ?>
                </div>

                <div style="margin-bottom: 18px; font-size: 22px; color: #1f2937;">
                    <?= nl2br(htmlspecialchars($question['question_text'])) ?>
                </div>

                <?php if (!empty($question['question_image'])): ?>
                    <div style="margin-bottom: 20px;">
                        <img src="<?= htmlspecialchars($question['question_image']) ?>"
                             alt="Image de la question <?= (int)$item['number'] ?>"
                             style="max-width:100%; max-height:320px; border-radius:14px; border:1px solid #e5e7eb;">
                    </div>
                <?php endif; ?>

                <?php if ($question['question_type'] === 'qcm'): ?>
                    <div style="
                        border: 1px solid #e5e7eb;
                        border-radius: 16px;
                        overflow: hidden;
                        margin-top: 18px;
                    ">
                        <div style="
                            display:grid;
                            grid-template-columns: 80px 180px 180px 220px 1fr;
                            background:#f8fafc;
                            font-weight:800;
                            color:#1f2937;
                            border-bottom:1px solid #e5e7eb;
                        ">
                            <div style="padding:16px; border-right:1px solid #e5e7eb;"></div>
                            <div style="padding:16px; border-right:1px solid #e5e7eb;">Réponse attendue</div>
                            <div style="padding:16px; border-right:1px solid #e5e7eb;">Réponse saisie</div>
                            <div style="padding:16px; border-right:1px solid #e5e7eb;">Discordance</div>
                            <div style="padding:16px;">Proposition</div>
                        </div>

                        <?php foreach ($item['qcm_rows'] as $row): ?>
                            <?php
                                $rowBg = $row['discordant'] ? '#fbe4e6' : '#dfeadf';
                                $discordText = $row['discordant'] ? 'Oui' : 'Non';
                                $yesNoColor = $row['discordant'] ? '#b42318' : '#166534';
                            ?>
                            <div style="
                                display:grid;
                                grid-template-columns: 80px 180px 180px 220px 1fr;
                                border-bottom:1px solid #e5e7eb;
                            ">
                                <div style="padding:16px; border-right:1px solid #e5e7eb; font-size:24px; color:#1f2937;">
                                    <?= htmlspecialchars($row['key']) ?>
                                </div>

                                <div style="padding:16px; border-right:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; font-size:24px;">
                                    <?= $row['expected'] ? '<span style="color:#16a34a;">☑</span>' : '<span style="color:#ef4444;">☐</span>' ?>
                                </div>

                                <div style="padding:16px; border-right:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; font-size:24px;">
                                    <?= $row['selected'] ? '<span style="color:#16a34a;">☑</span>' : '<span style="color:#ef4444;">☐</span>' ?>
                                </div>

                                <div style="padding:16px; border-right:1px solid #e5e7eb; background:<?= $rowBg ?>; color:<?= $yesNoColor ?>; font-weight:700;">
                                    <?= $discordText ?>
                                </div>

                                <div style="padding:16px; color:#374151;">
                                    <?= htmlspecialchars($row['text']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$isCorrect): ?>
                        <div style="
                            margin-top: 18px;
                            display:grid;
                            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                            gap: 16px;
                        ">
                            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:16px;">
                                <div style="font-weight:800; color:#991b1b; margin-bottom:8px;">Votre réponse</div>
                                <div style="color:#374151;"><?= htmlspecialchars($answer['answer_given'] ?? 'Aucune réponse') ?></div>
                            </div>

                            <div style="background:#ecfdf3; border:1px solid #bbf7d0; border-radius:14px; padding:16px;">
                                <div style="font-weight:800; color:#166534; margin-bottom:8px;">Bonne réponse</div>
                                <div style="color:#374151;"><?= htmlspecialchars($answer['correct_answer'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div style="
                        display:grid;
                        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                        gap: 18px;
                        margin-top: 20px;
                    ">
                        <div style="
                            background: #f9fafb;
                            border: 1px solid #e5e7eb;
                            border-radius: 16px;
                            padding: 18px;
                        ">
                            <div style="font-weight: 800; color: #374151; margin-bottom: 10px;">Réponse saisie</div>
                            <div style="
                                min-height: 110px;
                                background: white;
                                border: 1px solid #d1d5db;
                                border-radius: 12px;
                                padding: 14px;
                                color: #111827;
                                white-space: pre-wrap;
                            "><?= htmlspecialchars($answer['answer_given'] ?? 'Aucune réponse') ?></div>
                        </div>

                        <div style="
                            background: <?= $isCorrect ? '#ecfdf3' : '#fef2f2' ?>;
                            border: 1px solid <?= $isCorrect ? '#bbf7d0' : '#fecaca' ?>;
                            border-radius: 16px;
                            padding: 18px;
                        ">
                            <div style="font-weight: 800; color: <?= $isCorrect ? '#166534' : '#991b1b' ?>; margin-bottom: 10px;">
                                Bonne réponse attendue
                            </div>
                            <div style="
                                min-height: 110px;
                                background: white;
                                border: 1px solid #d1d5db;
                                border-radius: 12px;
                                padding: 14px;
                                color: #111827;
                                white-space: pre-wrap;
                            "><?= htmlspecialchars($answer['correct_answer'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <section style="max-width:1100px; margin:0 auto;">
        <div style="
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.07);
            display:flex;
            justify-content:center;
            gap:14px;
            flex-wrap:wrap;
        ">
            <a href="index.php?page=home" style="
                display:inline-block;
                text-decoration:none;
                background:#f97316;
                color:white;
                padding:14px 24px;
                border-radius:999px;
                font-weight:700;
                box-shadow: 0 8px 18px rgba(249, 115, 22, 0.25);
            ">
                Retour à l'accueil
            </a>

            <?php if ($isAdmin): ?>
                <a href="index.php?page=instructions" style="
                    display:inline-block;
                    text-decoration:none;
                    background:#e5e7eb;
                    color:#374151;
                    padding:14px 24px;
                    border-radius:999px;
                    font-weight:700;
                ">
                    Refaire le quiz
                </a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>