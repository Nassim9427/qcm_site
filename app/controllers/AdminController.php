<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';
require_once __DIR__ . '/../models/QuizQuestion.php';

class AdminController
{
    private $resultModel;
    private $questionModel;

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
    }

    public function index()
    {
        $nom = trim($_GET['nom'] ?? '');
        $prenom = trim($_GET['prenom'] ?? '');
        $email = trim($_GET['email'] ?? '');

        $results = $this->resultModel->getAllResultsWithFilters($nom, $prenom, $email);

        require __DIR__ . '/../views/admin_results.php';
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

    private function ensurePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=quiz_preview');
            exit();
        }
    }
}