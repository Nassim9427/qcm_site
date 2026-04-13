<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';

class HomeController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $database = new Database();
        $pdo = $database->getConnection();
        $quizResultModel = new QuizResult($pdo);
        $quizAlreadyTaken = $quizResultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id']);
    
        require __DIR__ . '/../views/home.php';
    }
}