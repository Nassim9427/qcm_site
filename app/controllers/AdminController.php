<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';

class AdminController
{
    private $resultModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: index.php?page=home');
            exit();
        }

        $database = new Database();
        $pdo = $database->getConnection();
        $this->resultModel = new QuizResult($pdo);
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
}