<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';
require_once __DIR__ . '/../models/QuizQuestion.php';
require_once __DIR__ . '/../models/AccessKey.php';

class QuizController
{
    private $pdo;
    private $resultModel;
    private $questionModel;
    private $accessKeyModel;
    private $quizDurationSeconds = 600;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->resultModel = new QuizResult($this->pdo);
        $this->questionModel = new QuizQuestion($this->pdo);
        $this->accessKeyModel = new AccessKey($this->pdo);
    }

    private function isAdmin()
    {
        return isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
    }

    private function clearQuizSession()
    {
        unset($_SESSION['quiz_started_at']);
        unset($_SESSION['quiz_ends_at']);
        unset($_SESSION['quiz_already_submitted']);
    }

    public function instructions()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if (!$this->isAdmin() && $this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        $this->clearQuizSession();

        require __DIR__ . '/../views/instructions.php';
    }

    public function start()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if (!$this->isAdmin() && $this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        $questions = $this->questionModel->getActiveQuestions();

        if (empty($questions)) {
            $_SESSION['quiz_error'] = "Aucune question active n'est disponible pour le moment.";
            header('Location: index.php?page=home');
            exit();
        }

        if (empty($_SESSION['quiz_started_at']) || empty($_SESSION['quiz_ends_at'])) {
            $_SESSION['quiz_started_at'] = time();
            $_SESSION['quiz_ends_at'] = time() + $this->quizDurationSeconds;
        }

        if (time() >= (int)$_SESSION['quiz_ends_at']) {
            $_SESSION['quiz_error'] = "Le temps du quiz est écoulé.";
            $this->clearQuizSession();
            header('Location: index.php?page=home');
            exit();
        }

        $_SESSION['quiz_already_submitted'] = false;
        $remainingSeconds = max(0, (int)$_SESSION['quiz_ends_at'] - time());

        require __DIR__ . '/../views/quiz.php';
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if (
            !$this->isAdmin()
            && $this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])
        ) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        if (
            !$this->isAdmin()
            && isset($_SESSION['quiz_already_submitted'])
            && $_SESSION['quiz_already_submitted'] === true
        ) {
            header('Location: index.php?page=result');
            exit();
        }

        if (empty($_SESSION['quiz_started_at']) || empty($_SESSION['quiz_ends_at'])) {
            $_SESSION['quiz_error'] = "Session de quiz invalide.";
            header('Location: index.php?page=home');
            exit();
        }

        $questions = $this->questionModel->getActiveQuestions();

        if (empty($questions)) {
            $_SESSION['quiz_error'] = "Aucune question active n'est disponible pour le moment.";
            header('Location: index.php?page=home');
            exit();
        }

        $score = 0;
        $totalQuestions = count($questions);
        $preparedAnswers = [];

        foreach ($questions as $question) {
            $fieldName = 'question_' . $question['id'];
            $rawAnswer = $_POST[$fieldName] ?? null;
            $isCorrect = false;
            $answerGivenForSave = null;
            $correctAnswerForSave = '';

            if ($question['question_type'] === 'qcm') {
                $optionsMap = [];

                foreach ($question['options'] as $option) {
                    $optionsMap[$option['key']] = $option['text'];
                }

                $answerKey = is_string($rawAnswer) ? trim($rawAnswer) : '';
                $correctKey = (string)$question['correct_answer'];

                $isCorrect = $answerKey !== '' && $answerKey === $correctKey;

                $answerGivenForSave = $answerKey !== ''
                    ? ($answerKey . ' - ' . ($optionsMap[$answerKey] ?? $answerKey))
                    : null;

                $correctAnswerForSave = $correctKey . ' - ' . ($optionsMap[$correctKey] ?? $correctKey);
            } else {
                $answerText = trim((string)$rawAnswer);
                $acceptedAnswers = $question['accepted_answers_list'] ?? [];

                $normalizedGiven = mb_strtolower($answerText, 'UTF-8');
                $normalizedAccepted = array_map(static function ($answer) {
                    return mb_strtolower(trim((string)$answer), 'UTF-8');
                }, $acceptedAnswers);

                $isCorrect = $answerText !== '' && in_array($normalizedGiven, $normalizedAccepted, true);
                $answerGivenForSave = $answerText !== '' ? $answerText : null;
                $correctAnswerForSave = implode(' / ', $acceptedAnswers);
            }

            if ($isCorrect) {
                $score++;
            }

            $preparedAnswers[] = [
                'field_name' => $fieldName,
                'answer_given' => $answerGivenForSave,
                'correct_answer' => $correctAnswerForSave,
                'is_correct' => $isCorrect,
            ];
        }

        if ($this->isAdmin()) {
            $resultId = $this->resultModel->createOrReplace($_SESSION['user_id'], $score, $totalQuestions);
        } else {
            $resultId = $this->resultModel->create($_SESSION['user_id'], $score, $totalQuestions);
        }

        foreach ($preparedAnswers as $answer) {
            $this->resultModel->saveAnswer(
                $resultId,
                $answer['field_name'],
                $answer['answer_given'],
                $answer['correct_answer'],
                $answer['is_correct']
            );
        }

        // La clé d'accès n'est consommée qu'au moment où le quiz est envoyé
        if (!$this->isAdmin() && !empty($_SESSION['access_id'])) {
            $this->accessKeyModel->markAsUsed((int)$_SESSION['access_id']);
            unset($_SESSION['access_id']);
        }

        $_SESSION['quiz_submitted'] = true;
        $_SESSION['quiz_score'] = $score;
        $_SESSION['quiz_total_questions'] = $totalQuestions;

        $this->clearQuizSession();

        header('Location: index.php?page=result');
        exit();
    }

    public function result()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $result = $this->resultModel->getResultByUserId($_SESSION['user_id']);

        if (!$result) {
            $_SESSION['quiz_error'] = "Aucun résultat trouvé.";
            header('Location: index.php?page=home');
            exit();
        }

        $answers = $this->resultModel->getAnswersByResultId($result['id']);
        $corrections = [];

        foreach ($answers as $index => $answer) {
            $questionId = 0;

            if (preg_match('/question_(\d+)/', (string)$answer['question_key'], $matches)) {
                $questionId = (int)$matches[1];
            }

            if ($questionId <= 0) {
                continue;
            }

            $question = $this->questionModel->getQuestionById($questionId);

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

        require __DIR__ . '/../views/result.php';
    }

    public function myResult()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $result = $this->resultModel->getResultByUserId($_SESSION['user_id']);

        require __DIR__ . '/../views/my_result.php';
    }
}