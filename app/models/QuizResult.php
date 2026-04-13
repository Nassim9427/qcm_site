<?php

class QuizResult
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function hasUserAlreadyTakenQuiz($userId)
    {
        $sql = "SELECT COUNT(*) FROM results WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchColumn() > 0;
    }

    public function create($userId, $score, $totalQuestions)
    {
        $sql = "INSERT INTO results (user_id, score, total_questions)
                VALUES (:user_id, :score, :total_questions)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId,
            'score' => $score,
            'total_questions' => $totalQuestions
        ]);

        return $this->pdo->lastInsertId();
    }

    public function saveAnswer($resultId, $questionKey, $answerGiven, $correctAnswer, $isCorrect)
    {
        $sql = "INSERT INTO quiz_answers (result_id, question_key, answer_given, correct_answer, is_correct)
                VALUES (:result_id, :question_key, :answer_given, :correct_answer, :is_correct)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'result_id' => $resultId,
            'question_key' => $questionKey,
            'answer_given' => $answerGiven,
            'correct_answer' => $correctAnswer,
            'is_correct' => $isCorrect ? 1 : 0
        ]);
    }

    public function getAllResultsWithFilters($nom = '', $prenom = '', $email = '')
    {
        $sql = "SELECT 
                    results.id,
                    results.user_id,
                    users.nom,
                    users.prenom,
                    users.email,
                    results.score,
                    results.total_questions,
                    results.created_at
                FROM results
                INNER JOIN users ON results.user_id = users.id
                WHERE users.nom LIKE :nom
                  AND users.prenom LIKE :prenom
                  AND users.email LIKE :email
                ORDER BY results.created_at DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => '%' . $nom . '%',
            'prenom' => '%' . $prenom . '%',
            'email' => '%' . $email . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnswersByResultId($resultId)
    {
        $sql = "SELECT *
                FROM quiz_answers
                WHERE result_id = :result_id
                ORDER BY id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['result_id' => $resultId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) AS total_tests,
                    AVG(score) AS moyenne_score,
                    MAX(score) AS meilleur_score
                FROM results";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRanking()
    {
        $sql = "SELECT 
                    users.nom,
                    users.prenom,
                    users.email,
                    results.score,
                    results.total_questions,
                    results.created_at
                FROM results
                INNER JOIN users ON results.user_id = users.id
                ORDER BY results.score DESC, results.created_at ASC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResultByUserId($userId)
    {
        $sql = "SELECT *
                FROM results
                WHERE user_id = :user_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}