<?php

class TestCode
{
    private $pdo;

    private $defaultCodes = [
        'project' => [
            'label' => 'Gestion de projet - Agile et Cycle en V',
            'code' => 'projet2026',
        ],
        'sql' => [
            'label' => 'SQL - Requêtes et logique',
            'code' => 'sql2026',
        ],
        'api' => [
            'label' => 'API - Webservices et échanges',
            'code' => 'api2026',
        ],
        'moa' => [
            'label' => 'MOA - Business Analyst',
            'code' => 'moa2026',
        ],
        'technique' => [
            'label' => 'Technique et automatisation',
            'code' => 'tech2026',
        ],
        'logic' => [
            'label' => 'Logique et cas pratiques',
            'code' => 'logique2026',
        ],
    ];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize()
    {
        $this->createTable();
        $this->addMissingColumns();
        $this->seedDefaultCodes();
        $this->fillMissingReadableCodes();
    }

    private function createTable()
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS test_codes (
                test_key VARCHAR(50) PRIMARY KEY,
                label VARCHAR(150) NOT NULL,
                code_value VARCHAR(255) DEFAULT NULL,
                code_hash VARCHAR(255) NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    private function addMissingColumns()
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM test_codes LIKE 'code_value'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            $this->pdo->exec("ALTER TABLE test_codes ADD COLUMN code_value VARCHAR(255) DEFAULT NULL AFTER label");
        }
    }

    private function seedDefaultCodes()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM test_codes WHERE test_key = ?");

        foreach ($this->defaultCodes as $key => $data) {
            $stmt->execute([$key]);
            $exists = (int)$stmt->fetchColumn() > 0;

            if (!$exists) {
                $insert = $this->pdo->prepare(
                    "INSERT INTO test_codes (test_key, label, code_value, code_hash)
                     VALUES (?, ?, ?, ?)"
                );

                $insert->execute([
                    $key,
                    $data['label'],
                    $data['code'],
                    password_hash($data['code'], PASSWORD_DEFAULT),
                ]);
            }
        }
    }

    private function fillMissingReadableCodes()
    {
        $select = $this->pdo->prepare("
            SELECT code_value
            FROM test_codes
            WHERE test_key = ?
            LIMIT 1
        ");

        $update = $this->pdo->prepare("
            UPDATE test_codes
            SET code_value = ?, code_hash = ?
            WHERE test_key = ?
        ");

        foreach ($this->defaultCodes as $key => $data) {
            $select->execute([$key]);
            $currentValue = $select->fetchColumn();

            if ($currentValue === false || trim((string)$currentValue) === '') {
                $update->execute([
                    $data['code'],
                    password_hash($data['code'], PASSWORD_DEFAULT),
                    $key,
                ]);
            }
        }
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT test_key, label, code_value, updated_at
            FROM test_codes
            ORDER BY FIELD(test_key, 'project', 'sql', 'api', 'moa', 'technique', 'logic')
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isValidTestKey($testKey)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM test_codes WHERE test_key = ?");
        $stmt->execute([$testKey]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getLabelByKey($testKey)
    {
        $stmt = $this->pdo->prepare("SELECT label FROM test_codes WHERE test_key = ? LIMIT 1");
        $stmt->execute([$testKey]);

        $label = $stmt->fetchColumn();

        return $label ?: null;
    }

    public function verifyCode($testKey, $submittedCode)
    {
        $stmt = $this->pdo->prepare("SELECT code_hash FROM test_codes WHERE test_key = ? LIMIT 1");
        $stmt->execute([$testKey]);

        $hash = $stmt->fetchColumn();

        if (!$hash) {
            return false;
        }

        return password_verify($submittedCode, $hash);
    }

    public function updateCode($testKey, $newCode)
    {
        $newCode = trim((string)$newCode);

        if ($newCode === '') {
            throw new InvalidArgumentException('Le mot de passe ne peut pas être vide.');
        }

        if (strlen($newCode) < 4) {
            throw new InvalidArgumentException('Le mot de passe doit contenir au moins 4 caractères.');
        }

        $stmt = $this->pdo->prepare("
            UPDATE test_codes
            SET code_value = ?, code_hash = ?
            WHERE test_key = ?
        ");

        return $stmt->execute([
            $newCode,
            password_hash($newCode, PASSWORD_DEFAULT),
            $testKey,
        ]);
    }
}