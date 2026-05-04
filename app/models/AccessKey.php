<?php

class AccessKey
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize()
    {
        $this->createTable();
    }

    private function createTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS access_keys (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                access_key VARCHAR(64) NOT NULL UNIQUE,
                nom VARCHAR(100) DEFAULT NULL,
                prenom VARCHAR(100) DEFAULT NULL,
                is_used TINYINT(1) NOT NULL DEFAULT 0,
                used_at DATETIME DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function generateUniqueKey($length = 12)
    {
        do {
            $key = strtoupper(substr(bin2hex(random_bytes(16)), 0, $length));
        } while ($this->keyExists($key));

        return $key;
    }

    private function keyExists($key)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM access_keys WHERE access_key = ?");
        $stmt->execute([$key]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM access_keys WHERE email = ?");
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createAccess($email)
    {
        $email = trim(mb_strtolower($email, 'UTF-8'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'adresse email est invalide.");
        }

        if ($this->emailExists($email)) {
            throw new InvalidArgumentException("Un accès existe déjà pour cette adresse email.");
        }

        $accessKey = $this->generateUniqueKey();

        $stmt = $this->pdo->prepare("
            INSERT INTO access_keys (email, access_key)
            VALUES (?, ?)
        ");
        $stmt->execute([$email, $accessKey]);

        return [
            'id' => (int)$this->pdo->lastInsertId(),
            'email' => $email,
            'access_key' => $accessKey,
        ];
    }

    public function getAllAccesses()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM access_keys
            ORDER BY created_at DESC, id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEmailAndKey($email, $accessKey)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM access_keys
            WHERE email = ? AND access_key = ?
            LIMIT 1
        ");
        $stmt->execute([
            trim(mb_strtolower($email, 'UTF-8')),
            trim($accessKey)
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function completeIdentityAndMarkUsed($id, $nom, $prenom)
    {
        $nom = trim($nom);
        $prenom = trim($prenom);

        if ($nom === '' || $prenom === '') {
            throw new InvalidArgumentException("Le nom et le prénom sont obligatoires.");
        }

        $stmt = $this->pdo->prepare("
            UPDATE access_keys
            SET nom = ?, prenom = ?
            WHERE id = ?
        ");

        return $stmt->execute([$nom, $prenom, $id]);
    }

    public function markAsUsed($id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE access_keys
            SET is_used = 1, used_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([(int)$id]);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM access_keys
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findOrCreateUserFromAccess($access)
    {
        $email = trim(mb_strtolower($access['email'] ?? '', 'UTF-8'));
        $nom = trim($access['nom'] ?? '');
        $prenom = trim($access['prenom'] ?? '');

        if ($email === '' || $nom === '' || $prenom === '') {
            throw new InvalidArgumentException("Données d'accès incomplètes.");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $update = $this->pdo->prepare("
                UPDATE users
                SET nom = ?, prenom = ?
                WHERE id = ?
            ");
            $update->execute([$nom, $prenom, $user['id']]);

            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $randomPasswordHash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        $insert = $this->pdo->prepare("
            INSERT INTO users (
                nom,
                prenom,
                email,
                telephone,
                entreprise,
                poste,
                mot_de_passe
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $insert->execute([
            $nom,
            $prenom,
            $email,
            null,
            null,
            null,
            $randomPasswordHash
        ]);

        $userId = (int)$this->pdo->lastInsertId();

        $stmtById = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmtById->execute([$userId]);
        return $stmtById->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM access_keys WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}