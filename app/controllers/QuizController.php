<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/QuizResult.php';

class QuizController
{
    private $resultModel;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->getConnection();
        $this->resultModel = new QuizResult($pdo);
    }

    public function instructions()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if ($this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        require __DIR__ . '/../views/instructions.php';
    }

    public function start()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if ($this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        $_SESSION['quiz_already_submitted'] = false;

        require __DIR__ . '/../views/quiz.php';
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if ($this->resultModel->hasUserAlreadyTakenQuiz($_SESSION['user_id'])) {
            $_SESSION['quiz_error'] = "Vous avez déjà passé ce test. Une seule tentative est autorisée.";
            header('Location: index.php?page=home');
            exit();
        }

        if (isset($_SESSION['quiz_already_submitted']) && $_SESSION['quiz_already_submitted'] === true) {
            header('Location: index.php?page=result');
            exit();
        }

        $reponsesUtilisateur = $_POST;

        $bonnesReponsesQCM = [
            'question_1' => 'c',
            'question_2' => 'b',
            'question_3' => 'd',
            'question_4' => 'b'
        ];

        $bonnesReponsesOuvertes = [
            'question_5' => ['1']
        ];

        $score = 0;
        $totalQuestions = 5;

        foreach ($bonnesReponsesQCM as $question => $bonneReponse) {
            if (
                isset($reponsesUtilisateur[$question]) &&
                $reponsesUtilisateur[$question] === $bonneReponse
            ) {
                $score++;
            }
        }

        foreach ($bonnesReponsesOuvertes as $question => $reponsesAcceptees) {
            $reponseDonnee = trim($reponsesUtilisateur[$question] ?? '');

            if (in_array($reponseDonnee, $reponsesAcceptees, true)) {
                $score++;
            }
        }

        $resultId = $this->resultModel->create($_SESSION['user_id'], $score, $totalQuestions);

        foreach ($bonnesReponsesQCM as $question => $bonneReponse) {
            $reponseDonnee = $reponsesUtilisateur[$question] ?? null;
            $isCorrect = ($reponseDonnee === $bonneReponse);

            $this->resultModel->saveAnswer(
                $resultId,
                $question,
                $reponseDonnee,
                $bonneReponse,
                $isCorrect
            );
        }

        foreach ($bonnesReponsesOuvertes as $question => $reponsesAcceptees) {
            $reponseDonnee = trim($reponsesUtilisateur[$question] ?? '');
            $bonneReponse = implode(' / ', $reponsesAcceptees);
            $isCorrect = in_array($reponseDonnee, $reponsesAcceptees, true);

            $this->resultModel->saveAnswer(
                $resultId,
                $question,
                $reponseDonnee !== '' ? $reponseDonnee : null,
                $bonneReponse,
                $isCorrect
            );
        }

        $_SESSION['quiz_already_submitted'] = true;
        $_SESSION['quiz_submitted'] = true;
        $_SESSION['quiz_score'] = $score;
        $_SESSION['quiz_total_questions'] = $totalQuestions;

        header('Location: index.php?page=result');
        exit();
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

    public function preview()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: index.php?page=home');
            exit();
        }

        require __DIR__ . '/../views/quiz_preview.php';
    }
}