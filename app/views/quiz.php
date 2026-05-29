<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background: #f5f5f5;
        }

        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .quiz-timer {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
        }

        .quiz-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .anti-cheat-warning-box {
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 22px;
            font-weight: 700;
            line-height: 1.5;
        }

        .question-block {
            margin-bottom: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
        }

        .question-block:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .question-block h2 {
            margin-bottom: 10px;
        }

        .question-block label {
            display: block;
            margin: 8px 0;
        }

        .question-block textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            resize: vertical;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .question-image {
            margin: 14px 0 18px;
        }

        .question-image img {
            max-width: 100%;
            max-height: 320px;
            border-radius: 14px;
            display: block;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: zoom-in;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .question-image img:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .question-image-hint {
            margin-top: 8px;
            font-size: 13px;
            color: #6b7280;
        }

        .btn-submit-quiz {
            background: orange;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-submit-quiz:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .image-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            display: none;
            z-index: 9999;
        }

        .image-modal.open {
            display: block;
        }

        .image-modal-content {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
        }

        .image-modal img {
            display: block;
            width: auto;
            height: auto;
            max-width: calc(100vw - 48px);
            max-height: calc(100vh - 48px);
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            background: white;
            cursor: default;
        }

        .image-modal-close {
            position: absolute;
            top: 18px;
            right: 22px;
            background: rgba(255, 255, 255, 0.18);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 44px;
            height: 44px;
            border-radius: 999px;
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
            z-index: 10000;
        }

        .image-modal-close:hover {
            background: rgba(255, 255, 255, 0.28);
        }
    </style>
</head>
<body>

    <div class="quiz-container">
        <div class="quiz-header">
            <h1>Quiz</h1>
            <div class="quiz-timer" id="timer">--:--</div>
        </div>

        <div class="anti-cheat-warning-box" id="anti-cheat-box">
            Attention : si vous quittez l’onglet ou la fenêtre du quiz, vous aurez 2 avertissements.
            À la 3e fois, le quiz sera envoyé automatiquement.
            <br>
            Avertissements : <span id="anti-cheat-count">0</span>/2
        </div>

        <form id="quiz-form" method="POST" action="index.php?page=submit_quiz" autocomplete="off">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-block">
                    <h2>Question <?= $index + 1 ?></h2>
                    <p><?= nl2br(htmlspecialchars($question['question_text'])) ?></p>

                    <?php if (!empty($question['question_image'])): ?>
                        <div class="question-image">
                            <img
                                src="<?= htmlspecialchars($question['question_image']) ?>"
                                alt="Illustration de la question <?= $index + 1 ?>"
                                class="js-zoomable-image"
                            >
                            <div class="question-image-hint">Clique sur l’image pour l’agrandir</div>
                        </div>
                    <?php endif; ?>

                    <?php if ($question['question_type'] === 'qcm'): ?>
                        <?php foreach ($question['options'] as $option): ?>
                            <label>
                                <input
                                    type="radio"
                                    name="question_<?= (int)$question['id'] ?>"
                                    value="<?= htmlspecialchars($option['key']) ?>"
                                >
                                <?= strtoupper(htmlspecialchars($option['key'])) ?>. <?= htmlspecialchars($option['text']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <textarea
                            name="question_<?= (int)$question['id'] ?>"
                            placeholder="Écris ta réponse ici..."></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-submit-quiz" id="submit-btn">Valider mes réponses</button>
        </form>
    </div>

    <div class="image-modal" id="image-modal" aria-hidden="true">
        <button type="button" class="image-modal-close" id="image-modal-close" aria-label="Fermer">×</button>
        <div class="image-modal-content" id="image-modal-content">
            <img src="" alt="Image agrandie" id="image-modal-img">
        </div>
    </div>

<script>
    let tempsRestant = <?= (int)$remainingSeconds ?>;

    const timerElement = document.getElementById('timer');
    const quizForm = document.getElementById('quiz-form');
    const submitBtn = document.getElementById('submit-btn');
    const antiCheatCountElement = document.getElementById('anti-cheat-count');

    let quizDejaEnvoye = false;
    let timerInterval = null;

    let antiCheatWarnings = 0;
    const maxAntiCheatWarnings = 2;
    let antiCheatAlertOpen = false;
    let ignoreAntiCheatUntil = 0;

    function formatTemps(secondesTotales) {
        const heures = Math.floor(secondesTotales / 3600);
        const minutes = Math.floor((secondesTotales % 3600) / 60);
        const secondes = secondesTotales % 60;

        if (heures > 0) {
            return String(heures).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(secondes).padStart(2, '0');
        }

        return String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');
    }

    function envoyerQuiz() {
        if (quizDejaEnvoye) {
            return;
        }

        quizDejaEnvoye = true;

        if (timerInterval !== null) {
            clearInterval(timerInterval);
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';
        quizForm.submit();
    }

    function updateAntiCheatDisplay() {
        if (antiCheatCountElement) {
            antiCheatCountElement.textContent = antiCheatWarnings;
        }
    }

    function gererTentativeAntiTriche(raison) {
        if (quizDejaEnvoye) {
            return;
        }

        const now = Date.now();

        if (antiCheatAlertOpen || now < ignoreAntiCheatUntil) {
            return;
        }

        const modal = document.getElementById('image-modal');

        if (modal && modal.classList.contains('open')) {
            return;
        }

        antiCheatWarnings++;
        updateAntiCheatDisplay();

        if (antiCheatWarnings <= maxAntiCheatWarnings) {
            antiCheatAlertOpen = true;

            alert(
                "Attention : vous avez quitté la page ou la fenêtre du quiz.\n\n" +
                "Raison détectée : " + raison + "\n\n" +
                "Avertissement " + antiCheatWarnings + "/2.\n" +
                "À la 3e fois, le quiz sera envoyé automatiquement."
            );

            ignoreAntiCheatUntil = Date.now() + 1500;

            setTimeout(function () {
                antiCheatAlertOpen = false;
            }, 300);

            return;
        }

        antiCheatAlertOpen = true;

        alert(
            "Troisième sortie détectée.\n\n" +
            "Le quiz va être envoyé automatiquement."
        );

        envoyerQuiz();
    }

    function updateTimer() {
        timerElement.textContent = formatTemps(tempsRestant);

        if (tempsRestant <= 0) {
            envoyerQuiz();
            return;
        }

        tempsRestant--;
    }

    quizForm.addEventListener('submit', function (event) {
        if (quizDejaEnvoye) {
            event.preventDefault();
            return;
        }

        quizDejaEnvoye = true;

        if (timerInterval !== null) {
            clearInterval(timerInterval);
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';
    });

    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    function lockHistoryNavigation() {
        history.replaceState({ quizLock: true, step: 0 }, '', location.href);
        history.pushState({ quizLock: true, step: 1 }, '', location.href);
        history.pushState({ quizLock: true, step: 2 }, '', location.href);
    }

    lockHistoryNavigation();

    window.addEventListener('popstate', function () {
        if (quizDejaEnvoye) {
            return;
        }

        history.pushState({ quizLock: true, step: Date.now() }, '', location.href);
    });

    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('image-modal-img');
    const modalClose = document.getElementById('image-modal-close');
    const modalContent = document.getElementById('image-modal-content');
    const zoomableImages = document.querySelectorAll('.js-zoomable-image');

    function openImageModal(src, alt) {
        modalImg.src = src;
        modalImg.alt = alt || 'Image agrandie';
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        ignoreAntiCheatUntil = Date.now() + 800;
    }

    function closeImageModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        modalImg.src = '';
        document.body.style.overflow = '';
        ignoreAntiCheatUntil = Date.now() + 800;
    }

    zoomableImages.forEach(function (image) {
        image.addEventListener('click', function () {
            openImageModal(image.src, image.alt);
        });
    });

    modalClose.addEventListener('click', function (event) {
        event.stopPropagation();
        closeImageModal();
    });

    modal.addEventListener('click', function () {
        closeImageModal();
    });

    modalContent.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('keydown', function (event) {
        const tag = event.target.tagName ? event.target.tagName.toLowerCase() : '';
        const isTypingField =
            tag === 'input' ||
            tag === 'textarea' ||
            event.target.isContentEditable;

        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeImageModal();
            return;
        }

        if (event.altKey && (event.key === 'ArrowLeft' || event.key === 'ArrowRight')) {
            event.preventDefault();
            return;
        }

        if (event.key === 'Backspace' && !isTypingField) {
            event.preventDefault();
            return;
        }

        if (
            (event.ctrlKey && (event.key === 'r' || event.key === 'R')) ||
            event.key === 'F5'
        ) {
            event.preventDefault();
            return;
        }

        if (event.metaKey && (event.key === 'r' || event.key === 'R')) {
            event.preventDefault();
            return;
        }

        if (event.key === 'BrowserBack' || event.key === 'BrowserForward') {
            event.preventDefault();
            return;
        }
    });

    window.addEventListener('beforeunload', function (event) {
        if (quizDejaEnvoye) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            gererTentativeAntiTriche("changement d’onglet ou réduction du navigateur");
        }
    });

    window.addEventListener('blur', function () {
        if (!document.hidden) {
            gererTentativeAntiTriche("sortie de la fenêtre du quiz");
        }
    });
</script>
</body>
</html>