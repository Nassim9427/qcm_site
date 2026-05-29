<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';

class HomeController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $database = new Database();
        $pdo = $database->getConnection();

        $quizResultModel = new QuizResult($pdo);

        $takenTestModes = [];

        if (!empty($_SESSION['user_id'])) {
            $takenTestModes = $quizResultModel->getTakenTestModesByUserId($_SESSION['user_id']);
        }

        $quizAlreadyTaken = in_array('all', $takenTestModes, true);

        require __DIR__ . '/../views/home.php';
    }
}