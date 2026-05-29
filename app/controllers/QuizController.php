<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';
require_once __DIR__ . '/../models/QuizQuestion.php';
require_once __DIR__ . '/../models/AccessKey.php';
require_once __DIR__ . '/../models/TestCode.php';

class QuizController
{
    private $pdo;
    private $resultModel;
    private $questionModel;
    private $accessKeyModel;
    private $testCodeModel;

    private $quizDurationSeconds = 3600;

    private $testModes = [
        'all' => [
            'label' => 'Test complet',
            'notion' => null,
        ],
        'project' => [
            'label' => 'Gestion de projet - Agile et Cycle en V',
            'notion' => 'Gestion de projet - Agile et Cycle en V',
        ],
        'sql' => [
            'label' => 'SQL - Requêtes et logique',
            'notion' => 'SQL - Requêtes et logique',
        ],
        'api' => [
            'label' => 'API - Webservices et échanges',
            'notion' => 'API - Webservices et échanges',
        ],
        'moa' => [
            'label' => 'MOA - Business Analyst',
            'notion' => 'MOA - Business Analyst',
        ],
        'technique' => [
            'label' => 'Technique et automatisation',
            'notion' => 'Technique et automatisation',
        ],
        'logic' => [
            'label' => 'Logique et cas pratiques',
            'notion' => 'Logique et cas pratiques',
        ],
    ];

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();

        $this->resultModel = new QuizResult($this->pdo);
        $this->questionModel = new QuizQuestion($this->pdo);
        $this->accessKeyModel = new AccessKey($this->pdo);
        $this->testCodeModel = new TestCode($this->pdo);
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

    private function clearSelectedTest()
    {
        unset($_SESSION['selected_test_mode']);
        unset($_SESSION['selected_test_label']);
        unset($_SESSION['selected_test_notion']);
        unset($_SESSION['test_code_verified']);
    }

    private function selectTestMode($testMode)
    {
        if (!isset($this->testModes[$testMode])) {
            $testMode = 'all';
        }

        $_SESSION['selected_test_mode'] = $testMode;
        $_SESSION['selected_test_label'] = $this->testModes[$testMode]['label'];
        $_SESSION['selected_test_notion'] = $this->testModes[$testMode]['notion'];
    }

    private function getSelectedTestMode()
    {
        $testMode = $_SESSION['selected_test_mode'] ?? 'all';

        if (!isset($this->testModes[$testMode])) {
            $testMode = 'all';
            $this->selectTestMode('all');
        }

        return $testMode;
    }

    private function getSelectedTestLabel()
    {
        $testMode = $this->getSelectedTestMode();
        return $this->testModes[$testMode]['label'];
    }

    private function getSelectedTestNotion()
    {
        $testMode = $this->getSelectedTestMode();
        return $this->testModes[$testMode]['notion'];
    }

    private function hasAlreadyTakenSelectedTest($userId)
    {
        if ($this->isAdmin()) {
            return false;
        }

        $testMode = $this->getSelectedTestMode();

        return $this->resultModel->hasUserAlreadyTakenQuizByMode($userId, $testMode);
    }

    private function getQuestionsForSelectedTest()
    {
        $notion = $this->getSelectedTestNotion();

        if ($notion === null) {
            return $this->questionModel->getActiveQuestions();
        }

        return $this->questionModel->getActiveQuestionsByNotion($notion);
    }

    private function buildNotionStats(array $corrections)
    {
        $stats = [];

        foreach ($corrections as $correction) {
            $question = $correction['question'] ?? [];
            $answer = $correction['answer'] ?? [];

            $notion = trim((string)($question['notion'] ?? 'Non classée'));

            if ($notion === '') {
                $notion = 'Non classée';
            }

            if (!isset($stats[$notion])) {
                $stats[$notion] = [
                    'notion' => $notion,
                    'total' => 0,
                    'correct' => 0,
                    'wrong' => 0,
                    'percentage' => 0,
                ];
            }

            $stats[$notion]['total']++;

            if (!empty($answer['is_correct'])) {
                $stats[$notion]['correct']++;
            } else {
                $stats[$notion]['wrong']++;
            }
        }

        foreach ($stats as &$stat) {
            if ($stat['total'] > 0) {
                $stat['percentage'] = round(($stat['correct'] / $stat['total']) * 100);
            }
        }
        unset($stat);

        uasort($stats, static function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return array_values($stats);
    }

    private function splitStrengthsAndWeaknesses(array $notionStats)
    {
        $strengths = [];
        $weaknesses = [];

        foreach ($notionStats as $stat) {
            if ((int)$stat['total'] <= 0) {
                continue;
            }

            if ((int)$stat['percentage'] >= 70) {
                $strengths[] = $stat;
            } else {
                $weaknesses[] = $stat;
            }
        }

        usort($strengths, static function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        usort($weaknesses, static function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

        return [
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
        ];
    }

    public function chooseTest()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $testMode = trim((string)($_GET['test'] ?? 'all'));

        if (!isset($this->testModes[$testMode])) {
            $testMode = 'all';
        }

        $this->clearQuizSession();
        $this->selectTestMode($testMode);

        if (!$this->isAdmin() && $this->resultModel->hasUserAlreadyTakenQuizByMode($_SESSION['user_id'], $testMode)) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
            header('Location: index.php?page=home');
            exit();
        }

        if ($testMode === 'all') {
            $_SESSION['test_code_verified'] = true;
            header('Location: index.php?page=instructions');
            exit();
        }

        header('Location: index.php?page=test_code&test=' . urlencode($testMode));
        exit();
    }

    public function showTestCode()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $requestedTest = trim((string)($_GET['test'] ?? ''));

        if (
            $requestedTest === '' ||
            $requestedTest === 'all' ||
            !isset($this->testModes[$requestedTest]) ||
            !$this->testCodeModel->isValidTestKey($requestedTest)
        ) {
            header('Location: index.php?page=home');
            exit();
        }

        if (!$this->isAdmin() && $this->resultModel->hasUserAlreadyTakenQuizByMode($_SESSION['user_id'], $requestedTest)) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
            header('Location: index.php?page=home');
            exit();
        }

        $selectedTestMode = $requestedTest;
        $selectedTestLabel = $this->testModes[$requestedTest]['label'];

        require __DIR__ . '/../views/test_code.php';
    }

    public function submitTestCode()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=home');
            exit();
        }

        $requestedTest = trim((string)($_POST['test'] ?? ''));
        $submittedCode = trim((string)($_POST['test_code'] ?? ''));

        if (
            $requestedTest === '' ||
            $requestedTest === 'all' ||
            !isset($this->testModes[$requestedTest]) ||
            !$this->testCodeModel->isValidTestKey($requestedTest)
        ) {
            header('Location: index.php?page=home');
            exit();
        }

        if (!$this->isAdmin() && $this->resultModel->hasUserAlreadyTakenQuizByMode($_SESSION['user_id'], $requestedTest)) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
            header('Location: index.php?page=home');
            exit();
        }

        if (!$this->testCodeModel->verifyCode($requestedTest, $submittedCode)) {
            $_SESSION['test_code_error'] = "Code incorrect. Vérifiez le code du test.";
            header('Location: index.php?page=test_code&test=' . urlencode($requestedTest));
            exit();
        }

        $this->selectTestMode($requestedTest);
        $_SESSION['test_code_verified'] = true;

        header('Location: index.php?page=instructions');
        exit();
    }

    public function instructions()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $testMode = $this->getSelectedTestMode();

        if ($this->hasAlreadyTakenSelectedTest($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
            header('Location: index.php?page=home');
            exit();
        }

        if ($testMode !== 'all' && empty($_SESSION['test_code_verified'])) {
            header('Location: index.php?page=test_code&test=' . urlencode($testMode));
            exit();
        }

        $this->clearQuizSession();

        $selectedTestLabel = $this->getSelectedTestLabel();

        require __DIR__ . '/../views/instructions.php';
    }

    public function start()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $testMode = $this->getSelectedTestMode();

        if ($this->hasAlreadyTakenSelectedTest($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
            header('Location: index.php?page=home');
            exit();
        }

        if ($testMode !== 'all' && empty($_SESSION['test_code_verified'])) {
            header('Location: index.php?page=test_code&test=' . urlencode($testMode));
            exit();
        }

        $questions = $this->getQuestionsForSelectedTest();

        if (empty($questions)) {
            $_SESSION['quiz_error'] = "Aucune question active n'est disponible pour ce test.";
            $this->clearSelectedTest();
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
        $selectedTestLabel = $this->getSelectedTestLabel();

        require __DIR__ . '/../views/quiz.php';
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $testMode = $this->getSelectedTestMode();
        $testLabel = $this->getSelectedTestLabel();

        if ($this->hasAlreadyTakenSelectedTest($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test.";
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

        if ($testMode !== 'all' && empty($_SESSION['test_code_verified'])) {
            header('Location: index.php?page=test_code&test=' . urlencode($testMode));
            exit();
        }

        $questions = $this->getQuestionsForSelectedTest();

        if (empty($questions)) {
            $_SESSION['quiz_error'] = "Aucune question active n'est disponible pour ce test.";
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
            $resultId = $this->resultModel->createOrReplace(
                $_SESSION['user_id'],
                $score,
                $totalQuestions,
                $testMode,
                $testLabel
            );
        } else {
            $resultId = $this->resultModel->create(
                $_SESSION['user_id'],
                $score,
                $totalQuestions,
                $testMode,
                $testLabel
            );
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

        $_SESSION['quiz_submitted'] = true;
        $_SESSION['quiz_score'] = $score;
        $_SESSION['quiz_total_questions'] = $totalQuestions;
        $_SESSION['quiz_selected_test_label'] = $testLabel;
        $_SESSION['quiz_result_id'] = $resultId;

        $this->clearQuizSession();

        header('Location: index.php?page=result');
        exit();
    }

    public function result()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $resultId = isset($_SESSION['quiz_result_id']) ? (int)$_SESSION['quiz_result_id'] : 0;

        if ($resultId > 0) {
            $result = $this->resultModel->getResultByIdForUser($resultId, $_SESSION['user_id']);
        } else {
            $result = $this->resultModel->getResultByUserId($_SESSION['user_id']);
        }

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

        $notionStats = $this->buildNotionStats($corrections);
        $strengthsAndWeaknesses = $this->splitStrengthsAndWeaknesses($notionStats);

        $strengths = $strengthsAndWeaknesses['strengths'];
        $weaknesses = $strengthsAndWeaknesses['weaknesses'];

        $selectedTestLabel = $result['test_label'] ?? ($_SESSION['quiz_selected_test_label'] ?? $this->getSelectedTestLabel());

        require __DIR__ . '/../views/result.php';
    }

    public function myResult()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $resultId = isset($_GET['result_id']) ? (int)$_GET['result_id'] : 0;

        if ($resultId <= 0) {
            $results = $this->resultModel->getAllResultsByUserId($_SESSION['user_id']);

            require __DIR__ . '/../views/my_results_list.php';
            return;
        }

        $result = $this->resultModel->getResultByIdForUser($resultId, $_SESSION['user_id']);

        if (!$result) {
            $_SESSION['quiz_error'] = "Correction introuvable.";
            header('Location: index.php?page=my_result');
            exit();
        }

        require __DIR__ . '/../views/my_result.php';
    }
}