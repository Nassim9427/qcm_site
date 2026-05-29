<?php

class QuizResult
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize()
    {
        $this->addMissingColumns();
        $this->fixIndexes();
    }

    private function addMissingColumns()
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM quiz_results LIKE 'test_mode'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            $this->pdo->exec("
                ALTER TABLE quiz_results 
                ADD COLUMN test_mode VARCHAR(50) NOT NULL DEFAULT 'all' AFTER total_questions
            ");
        }

        $stmt = $this->pdo->query("SHOW COLUMNS FROM quiz_results LIKE 'test_label'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            $this->pdo->exec("
                ALTER TABLE quiz_results 
                ADD COLUMN test_label VARCHAR(150) NOT NULL DEFAULT 'Test complet' AFTER test_mode
            ");
        }
    }

    private function fixIndexes()
    {
        $stmt = $this->pdo->query("SHOW INDEX FROM quiz_results WHERE Key_name = 'uniq_user_test_mode'");
        $compositeIndex = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$compositeIndex) {
            try {
                $this->pdo->exec("
                    ALTER TABLE quiz_results 
                    ADD UNIQUE KEY uniq_user_test_mode (user_id, test_mode)
                ");
            } catch (Throwable $e) {
                // L'index existe peut-être déjà ou d'anciennes données empêchent sa création.
            }
        }

        $stmt = $this->pdo->query("
            SHOW INDEX FROM quiz_results 
            WHERE Key_name = 'user_id' 
            AND Non_unique = 0
        ");
        $oldUniqueUserIndex = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($oldUniqueUserIndex) {
            try {
                $this->pdo->exec("ALTER TABLE quiz_results DROP INDEX user_id");
            } catch (Throwable $e) {
                // L'ancien index peut déjà avoir été supprimé.
            }
        }
    }

    public function hasUserAlreadyTakenQuiz($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM quiz_results 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function hasUserAlreadyTakenQuizByMode($userId, $testMode)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM quiz_results
            WHERE user_id = ?
            AND test_mode = ?
        ");

        $stmt->execute([$userId, $testMode]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getTakenTestModesByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT test_mode
            FROM quiz_results
            WHERE user_id = ?
        ");

        $stmt->execute([$userId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'test_mode');
    }

    public function create($userId, $score, $totalQuestions, $testMode = 'all', $testLabel = 'Test complet')
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quiz_results (user_id, score, total_questions, test_mode, test_label)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $score,
            $totalQuestions,
            $testMode,
            $testLabel
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createOrReplace($userId, $score, $totalQuestions, $testMode = 'all', $testLabel = 'Test complet')
    {
        $existing = $this->getResultByUserIdAndMode($userId, $testMode);

        if ($existing) {
            $resultId = (int)$existing['id'];

            $deleteAnswers = $this->pdo->prepare("DELETE FROM quiz_answers WHERE result_id = ?");
            $deleteAnswers->execute([$resultId]);

            $update = $this->pdo->prepare("
                UPDATE quiz_results
                SET score = ?,
                    total_questions = ?,
                    test_mode = ?,
                    test_label = ?,
                    created_at = NOW()
                WHERE id = ?
            ");

            $update->execute([
                $score,
                $totalQuestions,
                $testMode,
                $testLabel,
                $resultId
            ]);

            return $resultId;
        }

        return $this->create($userId, $score, $totalQuestions, $testMode, $testLabel);
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
            SELECT *
            FROM quiz_results
            WHERE user_id = ?
            ORDER BY created_at DESC, id DESC
            LIMIT 1
        ");

        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResultByUserIdAndMode($userId, $testMode)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_results
            WHERE user_id = ?
            AND test_mode = ?
            LIMIT 1
        ");

        $stmt->execute([$userId, $testMode]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResultByIdForUser($resultId, $userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_results
            WHERE id = ?
            AND user_id = ?
            LIMIT 1
        ");

        $stmt->execute([$resultId, $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResultById($resultId)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_results
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$resultId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllResultsByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_results
            WHERE user_id = ?
            ORDER BY created_at DESC, id DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllResultsByUserIdWithFilter($userId, $testMode = '')
    {
        $sql = "
            SELECT *
            FROM quiz_results
            WHERE user_id = ?
        ";

        $params = [$userId];

        if ($testMode !== '') {
            $sql .= " AND test_mode = ?";
            $params[] = $testMode;
        }

        $sql .= " ORDER BY created_at DESC, id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupedUsersWithResultFilters($nom = '', $prenom = '', $email = '', $testMode = '')
    {
        $sql = "
            SELECT
                u.id AS user_id,
                u.nom,
                u.prenom,
                u.email,
                COUNT(qr.id) AS total_tests,
                AVG(qr.score / NULLIF(qr.total_questions, 0) * 100) AS average_percentage,
                MAX(qr.score / NULLIF(qr.total_questions, 0) * 100) AS best_percentage,
                MAX(qr.created_at) AS last_test_date
            FROM users u
            INNER JOIN quiz_results qr ON qr.user_id = u.id
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

        if ($testMode !== '') {
            $sql .= " AND qr.test_mode = ?";
            $params[] = $testMode;
        }

        $sql .= "
            GROUP BY u.id, u.nom, u.prenom, u.email
            ORDER BY last_test_date DESC, u.nom ASC, u.prenom ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserWithResultsInfo($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, prenom, email
            FROM users
            WHERE id = ?
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

        $sql .= " ORDER BY qr.created_at DESC, qr.score DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnswersByResultId($resultId)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_answers
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

    public function getRanking()
    {
        $stmt = $this->pdo->query("
            SELECT 
                qr.id,
                qr.user_id,
                qr.score,
                qr.total_questions,
                qr.test_mode,
                qr.test_label,
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