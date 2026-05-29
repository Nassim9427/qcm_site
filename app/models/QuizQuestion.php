<?php

class QuizQuestion
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize()
    {
        $this->createTables();
        $this->addMissingColumns();
        $this->createUploadDirectory();
        $this->seedDefaultQuestionsIfEmpty();
    }

    private function createTables()
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS quiz_questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                question_text TEXT NOT NULL,
                question_image VARCHAR(255) DEFAULT NULL,
                question_type VARCHAR(20) NOT NULL DEFAULT 'qcm',
                notion VARCHAR(150) NOT NULL DEFAULT 'Non classée',
                correct_answer VARCHAR(255) DEFAULT NULL,
                accepted_answers TEXT DEFAULT NULL,
                position INT NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS quiz_options (
                id INT AUTO_INCREMENT PRIMARY KEY,
                question_id INT NOT NULL,
                option_key VARCHAR(10) NOT NULL,
                option_text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_quiz_options_question
                    FOREIGN KEY (question_id) REFERENCES quiz_questions(id)
                    ON DELETE CASCADE,
                UNIQUE KEY uniq_question_option (question_id, option_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    private function addMissingColumns()
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM quiz_questions LIKE 'question_image'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            $this->pdo->exec("ALTER TABLE quiz_questions ADD COLUMN question_image VARCHAR(255) DEFAULT NULL AFTER question_text");
        }

        $stmt = $this->pdo->query("SHOW COLUMNS FROM quiz_questions LIKE 'notion'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            $this->pdo->exec("ALTER TABLE quiz_questions ADD COLUMN notion VARCHAR(150) NOT NULL DEFAULT 'Non classée' AFTER question_type");
        }
    }

    private function createUploadDirectory()
    {
        $dir = $this->getUploadDirectoryAbsolutePath();

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    private function seedDefaultQuestionsIfEmpty()
    {
        $count = (int)$this->pdo->query("SELECT COUNT(*) FROM quiz_questions")->fetchColumn();

        if ($count > 0) {
            return;
        }

        $defaultQuestions = [
            [
                'question_text' => 'Quelle est la capitale de la France ?',
                'question_type' => 'qcm',
                'notion' => 'Non classée',
                'correct_answer' => 'c',
                'position' => 1,
                'options' => [
                    'a' => 'Berlin',
                    'b' => 'Madrid',
                    'c' => 'Paris',
                    'd' => 'Rome',
                ],
            ],
            [
                'question_text' => 'Combien font 2 + 2 ?',
                'question_type' => 'qcm',
                'notion' => 'Non classée',
                'correct_answer' => 'b',
                'position' => 2,
                'options' => [
                    'a' => '3',
                    'b' => '4',
                    'c' => '5',
                    'd' => '6',
                ],
            ],
            [
                'question_text' => 'Quel mot-clé SQL permet de récupérer des données ?',
                'question_type' => 'qcm',
                'notion' => 'SQL - Requêtes et logique',
                'correct_answer' => 'd',
                'position' => 3,
                'options' => [
                    'a' => 'INSERT',
                    'b' => 'UPDATE',
                    'c' => 'DELETE',
                    'd' => 'SELECT',
                ],
            ],
            [
                'question_text' => 'Quel type de test vérifie le bon fonctionnement global d’une fonctionnalité côté utilisateur ?',
                'question_type' => 'qcm',
                'notion' => 'Gestion de projet - Agile et Cycle en V',
                'correct_answer' => 'b',
                'position' => 4,
                'options' => [
                    'a' => 'Test unitaire',
                    'b' => 'Test fonctionnel',
                    'c' => 'Test de charge',
                    'd' => 'Test réseau',
                ],
            ],
            [
                'question_text' => 'Écris uniquement le chiffre 1 dans la zone de texte.',
                'question_type' => 'text',
                'notion' => 'Non classée',
                'accepted_answers' => "1",
                'position' => 5,
                'options' => [],
            ],
        ];

        foreach ($defaultQuestions as $question) {
            $this->createQuestion($question);
        }
    }

    public function getActiveQuestions()
    {
        return $this->getQuestions(false);
    }

    public function getActiveQuestionsByNotion($notion)
    {
        return $this->getQuestions(false, $notion);
    }

    public function getAllQuestions()
    {
        return $this->getQuestions(true);
    }

    private function getQuestions($includeInactive, $notion = null)
    {
        $sql = "SELECT * FROM quiz_questions";
        $conditions = [];
        $params = [];

        if (!$includeInactive) {
            $conditions[] = "is_active = 1";
        }

        if ($notion !== null && trim((string)$notion) !== '') {
            $conditions[] = "notion = ?";
            $params[] = trim((string)$notion);
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY position ASC, id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($questions)) {
            return [];
        }

        $questionIds = array_column($questions, 'id');
        $placeholders = implode(',', array_fill(0, count($questionIds), '?'));

        $optionsStmt = $this->pdo->prepare(
            "SELECT question_id, option_key, option_text
             FROM quiz_options
             WHERE question_id IN ($placeholders)
             ORDER BY FIELD(option_key, 'a', 'b', 'c', 'd', 'e', 'f'), id ASC"
        );
        $optionsStmt->execute($questionIds);
        $optionsRows = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

        $optionsByQuestion = [];
        foreach ($optionsRows as $row) {
            $optionsByQuestion[$row['question_id']][] = [
                'key' => $row['option_key'],
                'text' => $row['option_text'],
            ];
        }

        foreach ($questions as &$question) {
            $question['notion'] = $question['notion'] ?? 'Non classée';
            $question['options'] = $optionsByQuestion[$question['id']] ?? [];
            $question['accepted_answers_list'] = $this->decodeAcceptedAnswers($question['accepted_answers'] ?? null);
        }
        unset($question);

        return $questions;
    }

    public function getQuestionById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_questions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) {
            return null;
        }

        $optionStmt = $this->pdo->prepare(
            "SELECT option_key, option_text
             FROM quiz_options
             WHERE question_id = :question_id
             ORDER BY FIELD(option_key, 'a', 'b', 'c', 'd', 'e', 'f'), id ASC"
        );
        $optionStmt->execute(['question_id' => $id]);

        $question['notion'] = $question['notion'] ?? 'Non classée';

        $question['options'] = array_map(static function ($row) {
            return [
                'key' => $row['option_key'],
                'text' => $row['option_text'],
            ];
        }, $optionStmt->fetchAll(PDO::FETCH_ASSOC));

        $question['accepted_answers_list'] = $this->decodeAcceptedAnswers($question['accepted_answers'] ?? null);

        return $question;
    }

    public function createQuestion(array $data)
    {
        $payload = $this->validateAndNormalize($data);

        $savedImagePath = null;

        if ($payload['image_data'] !== '') {
            $savedImagePath = $this->storeBase64Image($payload['image_data']);
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO quiz_questions (question_text, question_image, question_type, notion, correct_answer, accepted_answers, position, is_active)
                 VALUES (:question_text, :question_image, :question_type, :notion, :correct_answer, :accepted_answers, :position, :is_active)"
            );

            $stmt->execute([
                'question_text' => $payload['question_text'],
                'question_image' => $savedImagePath,
                'question_type' => $payload['question_type'],
                'notion' => $payload['notion'],
                'correct_answer' => $payload['correct_answer'],
                'accepted_answers' => $payload['accepted_answers'],
                'position' => $payload['position'],
                'is_active' => $payload['is_active'],
            ]);

            $questionId = (int)$this->pdo->lastInsertId();
            $this->replaceOptions($questionId, $payload['options']);

            $this->pdo->commit();
            return $questionId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($savedImagePath) {
                $this->deleteImageFile($savedImagePath);
            }

            throw $e;
        }
    }

    public function updateQuestion($id, array $data)
    {
        $existing = $this->getQuestionById($id);

        if (!$existing) {
            throw new RuntimeException('Question introuvable.');
        }

        $payload = $this->validateAndNormalize($data);

        $currentImagePath = $existing['question_image'] ?? null;
        $finalImagePath = $currentImagePath;
        $savedNewImagePath = null;
        $oldImageToDelete = null;

        if ($payload['image_data'] !== '') {
            $savedNewImagePath = $this->storeBase64Image($payload['image_data']);
            $finalImagePath = $savedNewImagePath;

            if (!empty($currentImagePath)) {
                $oldImageToDelete = $currentImagePath;
            }
        } elseif ($payload['remove_image']) {
            $finalImagePath = null;

            if (!empty($currentImagePath)) {
                $oldImageToDelete = $currentImagePath;
            }
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE quiz_questions
                 SET question_text = :question_text,
                     question_image = :question_image,
                     question_type = :question_type,
                     notion = :notion,
                     correct_answer = :correct_answer,
                     accepted_answers = :accepted_answers,
                     position = :position,
                     is_active = :is_active
                 WHERE id = :id"
            );

            $stmt->execute([
                'id' => $id,
                'question_text' => $payload['question_text'],
                'question_image' => $finalImagePath,
                'question_type' => $payload['question_type'],
                'notion' => $payload['notion'],
                'correct_answer' => $payload['correct_answer'],
                'accepted_answers' => $payload['accepted_answers'],
                'position' => $payload['position'],
                'is_active' => $payload['is_active'],
            ]);

            $this->replaceOptions($id, $payload['options']);

            $this->pdo->commit();

            if ($oldImageToDelete && $oldImageToDelete !== $savedNewImagePath) {
                $this->deleteImageFile($oldImageToDelete);
            }

            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($savedNewImagePath) {
                $this->deleteImageFile($savedNewImagePath);
            }

            throw $e;
        }
    }

    public function deleteQuestion($id)
    {
        $question = $this->getQuestionById($id);

        $stmt = $this->pdo->prepare("DELETE FROM quiz_questions WHERE id = :id");
        $deleted = $stmt->execute(['id' => $id]);

        if ($deleted && !empty($question['question_image'])) {
            $this->deleteImageFile($question['question_image']);
        }

        return $deleted;
    }

    private function replaceOptions($questionId, array $options)
    {
        $deleteStmt = $this->pdo->prepare("DELETE FROM quiz_options WHERE question_id = :question_id");
        $deleteStmt->execute(['question_id' => $questionId]);

        if (empty($options)) {
            return;
        }

        $insertStmt = $this->pdo->prepare(
            "INSERT INTO quiz_options (question_id, option_key, option_text)
             VALUES (:question_id, :option_key, :option_text)"
        );

        foreach ($options as $option) {
            $insertStmt->execute([
                'question_id' => $questionId,
                'option_key' => $option['key'],
                'option_text' => $option['text'],
            ]);
        }
    }

    private function validateAndNormalize(array $data)
    {
        $questionText = trim((string)($data['question_text'] ?? ''));
        $questionType = trim((string)($data['question_type'] ?? 'qcm'));
        $notion = trim((string)($data['notion'] ?? 'Non classée'));
        $position = isset($data['position']) ? (int)$data['position'] : $this->getNextPosition();
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;
        $imageData = trim((string)($data['image_data'] ?? ''));
        $removeImage = isset($data['remove_image']) && (int)$data['remove_image'] === 1;

        if ($questionText === '') {
            throw new InvalidArgumentException('Le texte de la question est obligatoire.');
        }

        if (!in_array($questionType, ['qcm', 'text'], true)) {
            throw new InvalidArgumentException('Le type de question est invalide.');
        }

        if ($notion === '') {
            $notion = 'Non classée';
        }

        if ($position <= 0) {
            $position = $this->getNextPosition();
        }

        $normalized = [
            'question_text' => $questionText,
            'question_type' => $questionType,
            'notion' => $notion,
            'correct_answer' => null,
            'accepted_answers' => null,
            'position' => $position,
            'is_active' => $isActive === 0 ? 0 : 1,
            'options' => [],
            'image_data' => $imageData,
            'remove_image' => $removeImage,
        ];

        if ($imageData !== '' && !$this->isValidBase64Image($imageData)) {
            throw new InvalidArgumentException('Le format de l’image est invalide. Utilise PNG, JPG, WEBP ou GIF.');
        }

        if ($questionType === 'qcm') {
            $rawOptions = $data['options'] ?? [];
            $letters = ['a', 'b', 'c', 'd', 'e', 'f'];
            $options = [];

            foreach ($letters as $letter) {
                $value = trim((string)($rawOptions[$letter] ?? ''));
                if ($value !== '') {
                    $options[] = [
                        'key' => $letter,
                        'text' => $value,
                    ];
                }
            }

            if (count($options) < 2) {
                throw new InvalidArgumentException('Une question QCM doit avoir au moins 2 propositions.');
            }

            $correctAnswer = trim((string)($data['correct_answer'] ?? ''));
            $allowedKeys = array_column($options, 'key');

            if (!in_array($correctAnswer, $allowedKeys, true)) {
                throw new InvalidArgumentException('La bonne réponse du QCM doit correspondre à une proposition remplie.');
            }

            $normalized['correct_answer'] = $correctAnswer;
            $normalized['options'] = $options;
        } else {
            $acceptedAnswersText = trim((string)($data['accepted_answers'] ?? ''));
            $answers = preg_split('/\r\n|\r|\n/', $acceptedAnswersText);
            $answers = array_values(array_filter(array_map(static function ($answer) {
                return trim((string)$answer);
            }, $answers), static function ($answer) {
                return $answer !== '';
            }));

            if (empty($answers)) {
                throw new InvalidArgumentException('Ajoute au moins une réponse acceptée pour la question texte.');
            }

            $normalized['accepted_answers'] = json_encode($answers, JSON_UNESCAPED_UNICODE);
        }

        return $normalized;
    }

    private function decodeAcceptedAnswers($acceptedAnswers)
    {
        if (!$acceptedAnswers) {
            return [];
        }

        $decoded = json_decode($acceptedAnswers, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function getNextPosition()
    {
        $max = (int)$this->pdo->query("SELECT COALESCE(MAX(position), 0) FROM quiz_questions")->fetchColumn();
        return $max + 1;
    }

    private function isValidBase64Image($dataUri)
    {
        return (bool)preg_match('/^data:image\/(png|jpeg|jpg|webp|gif);base64,/', $dataUri);
    }

    private function storeBase64Image($dataUri)
    {
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp|gif);base64,(.*)$/s', $dataUri, $matches)) {
            throw new InvalidArgumentException('Format d’image non reconnu.');
        }

        $mimeExtension = strtolower($matches[1]);
        $base64Content = $matches[2];
        $binary = base64_decode(str_replace(' ', '+', $base64Content), true);

        if ($binary === false) {
            throw new InvalidArgumentException('Impossible de décoder l’image.');
        }

        if (strlen($binary) > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('L’image est trop lourde. Taille max : 5 Mo.');
        }

        $extension = $mimeExtension === 'jpeg' ? 'jpg' : $mimeExtension;
        $filename = 'uploads/questions/' . uniqid('question_', true) . '.' . $extension;
        $absolutePath = $this->absolutePathFromRelative($filename);

        if (file_put_contents($absolutePath, $binary) === false) {
            throw new RuntimeException('Impossible d’enregistrer l’image.');
        }

        return $filename;
    }

    private function deleteImageFile($relativePath)
    {
        if (!$relativePath) {
            return;
        }

        $absolutePath = $this->absolutePathFromRelative($relativePath);

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function absolutePathFromRelative($relativePath)
    {
        return dirname(__DIR__, 2) . '/public/' . ltrim($relativePath, '/');
    }

    private function getUploadDirectoryAbsolutePath()
    {
        return dirname(__DIR__, 2) . '/public/uploads/questions';
    }
}