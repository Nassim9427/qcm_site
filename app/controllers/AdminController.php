<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';
require_once __DIR__ . '/../models/QuizQuestion.php';
require_once __DIR__ . '/../models/TestCode.php';

class AdminController
{
    private $resultModel;
    private $questionModel;
    private $testCodeModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: index.php?page=home');
            exit();
        }

        $database = new Database();
        $pdo = $database->getConnection();

        $this->resultModel = new QuizResult($pdo);
        $this->questionModel = new QuizQuestion($pdo);
        $this->testCodeModel = new TestCode($pdo);
    }

    public function index()
    {
        $nom = trim($_GET['nom'] ?? '');
        $prenom = trim($_GET['prenom'] ?? '');
        $email = trim($_GET['email'] ?? '');
        $testMode = trim($_GET['test_mode'] ?? '');

        $candidates = $this->resultModel->getGroupedUsersWithResultFilters($nom, $prenom, $email, $testMode);

        require __DIR__ . '/../views/admin_results.php';
    }

    public function userResults()
    {
        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        $testMode = trim($_GET['test_mode'] ?? '');

        if ($userId <= 0) {
            $_SESSION['admin_results_error'] = 'Candidat introuvable.';
            header('Location: index.php?page=admin_results');
            exit();
        }

        $candidate = $this->resultModel->getUserWithResultsInfo($userId);

        if (!$candidate) {
            $_SESSION['admin_results_error'] = 'Candidat introuvable.';
            header('Location: index.php?page=admin_results');
            exit();
        }

        $results = $this->resultModel->getAllResultsByUserIdWithFilter($userId, $testMode);

        require __DIR__ . '/../views/admin_user_results.php';
    }

    public function details()
    {
        $resultId = $_GET['result_id'] ?? null;

        if (!$resultId) {
            echo "Résultat introuvable.";
            return;
        }

        $answers = $this->resultModel->getAnswersByResultId($resultId);

        require __DIR__ . '/../views/admin_result_details.php';
    }

    public function stats()
    {
        $stats = $this->resultModel->getStats();
        $ranking = $this->resultModel->getRanking();

        require __DIR__ . '/../views/admin_stats.php';
    }

    public function quizManager()
    {
        $questions = $this->questionModel->getAllQuestions();

        require __DIR__ . '/../views/quiz_preview.php';
    }

    public function createQuestion()
    {
        $this->ensurePostRequest();

        try {
            $this->questionModel->createQuestion($_POST);
            $_SESSION['admin_quiz_success'] = 'La question a bien été ajoutée.';
        } catch (Throwable $e) {
            $_SESSION['admin_quiz_error'] = $e->getMessage();
        }

        header('Location: index.php?page=quiz_preview');
        exit();
    }

    public function updateQuestion()
    {
        $this->ensurePostRequest();

        $questionId = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;

        if ($questionId <= 0) {
            $_SESSION['admin_quiz_error'] = 'Question introuvable.';
            header('Location: index.php?page=quiz_preview');
            exit();
        }

        try {
            $this->questionModel->updateQuestion($questionId, $_POST);
            $_SESSION['admin_quiz_success'] = 'La question a bien été mise à jour.';
        } catch (Throwable $e) {
            $_SESSION['admin_quiz_error'] = $e->getMessage();
        }

        header('Location: index.php?page=quiz_preview');
        exit();
    }

    public function updateMultipleQuestions()
    {
        $this->ensurePostRequest();

        $questionsData = $_POST['questions'] ?? [];

        if (empty($questionsData) || !is_array($questionsData)) {
            $_SESSION['admin_quiz_error'] = 'Aucune modification reçue.';
            header('Location: index.php?page=quiz_preview');
            exit();
        }

        $updatedCount = 0;
        $errors = [];

        foreach ($questionsData as $questionId => $questionData) {
            $questionId = (int)$questionId;

            if ($questionId <= 0 || !is_array($questionData)) {
                continue;
            }

            try {
                $this->questionModel->updateQuestion($questionId, $questionData);
                $updatedCount++;
            } catch (Throwable $e) {
                $errors[] = 'Question ' . $questionId . ' : ' . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $_SESSION['admin_quiz_error'] = implode(' | ', $errors);
        } else {
            $_SESSION['admin_quiz_success'] = $updatedCount . ' question(s) mise(s) à jour.';
        }

        header('Location: index.php?page=quiz_preview');
        exit();
    }

    public function bulkUpdateQuestions()
    {
        $this->updateMultipleQuestions();
    }

    public function deleteQuestion()
    {
        $this->ensurePostRequest();

        $questionId = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;

        if ($questionId <= 0) {
            $_SESSION['admin_quiz_error'] = 'Question introuvable.';
            header('Location: index.php?page=quiz_preview');
            exit();
        }

        try {
            $this->questionModel->deleteQuestion($questionId);
            $_SESSION['admin_quiz_success'] = 'La question a bien été supprimée.';
        } catch (Throwable $e) {
            $_SESSION['admin_quiz_error'] = $e->getMessage();
        }

        header('Location: index.php?page=quiz_preview');
        exit();
    }

    public function testCodes()
    {
        $testCodes = $this->testCodeModel->getAll();

        require __DIR__ . '/../views/admin_test_codes.php';
    }

    public function updateTestCode()
    {
        $this->ensurePostRequest();

        $testKey = trim((string)($_POST['test_key'] ?? ''));
        $newCode = trim((string)($_POST['new_code'] ?? ''));

        if ($testKey === '') {
            $_SESSION['test_code_error'] = 'Test introuvable.';
            header('Location: index.php?page=admin_test_codes');
            exit();
        }

        try {
            $this->testCodeModel->updateCode($testKey, $newCode);
            $_SESSION['test_code_success'] = 'Le mot de passe du test a bien été modifié.';
        } catch (Throwable $e) {
            $_SESSION['test_code_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_test_codes');
        exit();
    }

    private function ensurePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=quiz_preview');
            exit();
        }
    }
}