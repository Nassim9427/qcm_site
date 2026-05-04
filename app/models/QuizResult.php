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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create($userId, $score, $totalQuestions)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quiz_results (user_id, score, total_questions)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $score, $totalQuestions]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createOrReplace($userId, $score, $totalQuestions)
    {
        $existing = $this->getResultByUserId($userId);

        if ($existing) {
            $resultId = (int)$existing['id'];

            $deleteAnswers = $this->pdo->prepare("DELETE FROM quiz_answers WHERE result_id = ?");
            $deleteAnswers->execute([$resultId]);

            $update = $this->pdo->prepare("
                UPDATE quiz_results
                SET score = ?, total_questions = ?, created_at = NOW()
                WHERE id = ?
            ");
            $update->execute([$score, $totalQuestions, $resultId]);

            return $resultId;
        }

        return $this->create($userId, $score, $totalQuestions);
    }

    public function saveAnswer($resultId, $questionKey, $answerGiven, $correctAnswer, $isCorrect)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quiz_answers (result_id, question_key, answer_given, correct_answer, is_correct)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $resultId,
            $questionKey,
            $answerGiven,
            $correctAnswer,
            $isCorrect ? 1 : 0
        ]);
    }

    public function getResultByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quiz_results
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllResultsWithFilters($nom = '', $prenom = '', $email = '')
    {
        $sql = "
            SELECT qr.*, u.nom, u.prenom, u.email
            FROM quiz_results qr
            JOIN users u ON qr.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if ($nom !== '') {
            $sql .= " AND u.nom LIKE ?";
            $params[] = '%' . $nom . '%';
        }

        if ($prenom !== '') {
            $sql .= " AND u.prenom LIKE ?";
            $params[] = '%' . $prenom . '%';
        }

        if ($email !== '') {
            $sql .= " AND u.email LIKE ?";
            $params[] = '%' . $email . '%';
        }

        $sql .= " ORDER BY qr.score DESC, qr.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnswersByResultId($resultId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quiz_answers
            WHERE result_id = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$resultId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStats()
    {
        $stmt = $this->pdo->query("
            SELECT
                COUNT(*) as total_participants,
                AVG(score) as average_score,
                MAX(score) as best_score,
                MIN(score) as worst_score
            FROM quiz_results
        ");

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔥 FIX ICI → AJOUT DU EMAIL
    public function getRanking()
    {
        $stmt = $this->pdo->query("
            SELECT 
                qr.id,
                qr.user_id,
                qr.score,
                qr.total_questions,
                qr.created_at,
                u.nom,
                u.prenom,
                u.email
            FROM quiz_results qr
            INNER JOIN users u ON qr.user_id = u.id
            ORDER BY qr.score DESC, qr.created_at ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}