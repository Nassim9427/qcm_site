<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
            margin-bottom: 30px;
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
            border-radius: 12px;
        }

        .question-block {
            margin-bottom: 30px;
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
            border-radius: 8px;
            resize: vertical;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .btn-submit-quiz {
            background: orange;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-submit-quiz:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <div class="quiz-container">
        <div class="quiz-header">
            <h1>Quiz</h1>
            <div class="quiz-timer" id="timer">10:00</div>
        </div>

        <form id="quiz-form" method="POST" action="index.php?page=submit_quiz">
            <div class="question-block">
                <h2>Question 1</h2>
                <p>Quelle est la capitale de la France ?</p>

                <label><input type="radio" name="question_1" value="a"> Berlin</label>
                <label><input type="radio" name="question_1" value="b"> Madrid</label>
                <label><input type="radio" name="question_1" value="c"> Paris</label>
                <label><input type="radio" name="question_1" value="d"> Rome</label>
            </div>

            <div class="question-block">
                <h2>Question 2</h2>
                <p>Combien font 2 + 2 ?</p>

                <label><input type="radio" name="question_2" value="a"> 3</label>
                <label><input type="radio" name="question_2" value="b"> 4</label>
                <label><input type="radio" name="question_2" value="c"> 5</label>
                <label><input type="radio" name="question_2" value="d"> 6</label>
            </div>

            <div class="question-block">
                <h2>Question 3</h2>
                <p>Quel mot-clé SQL permet de récupérer des données ?</p>

                <label><input type="radio" name="question_3" value="a"> INSERT</label>
                <label><input type="radio" name="question_3" value="b"> UPDATE</label>
                <label><input type="radio" name="question_3" value="c"> DELETE</label>
                <label><input type="radio" name="question_3" value="d"> SELECT</label>
            </div>

            <div class="question-block">
                <h2>Question 4</h2>
                <p>Quel type de test vérifie le bon fonctionnement global d’une fonctionnalité côté utilisateur ?</p>

                <label><input type="radio" name="question_4" value="a"> Test unitaire</label>
                <label><input type="radio" name="question_4" value="b"> Test fonctionnel</label>
                <label><input type="radio" name="question_4" value="c"> Test de charge</label>
                <label><input type="radio" name="question_4" value="d"> Test réseau</label>
            </div>

            <div class="question-block">
                <h2>Question 5</h2>
                <p>Écris uniquement le chiffre <strong>1</strong> dans la zone ci-dessous.</p>

                <textarea name="question_5" placeholder="Écris ta réponse ici..."></textarea>
            </div>

            <button type="submit" class="btn-submit-quiz" id="submit-btn">Valider mes réponses</button>
        </form>
    </div>

    <script>
        let tempsRestant = 600;
        const timerElement = document.getElementById('timer');
        const quizForm = document.getElementById('quiz-form');
        const submitBtn = document.getElementById('submit-btn');

        let quizDejaEnvoye = false;
        let timerInterval = null;

        function formatTemps(secondesTotales) {
            const minutes = Math.floor(secondesTotales / 60);
            const secondes = secondesTotales % 60;

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
    </script>

</body>
</html>