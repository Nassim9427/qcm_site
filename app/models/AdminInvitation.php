<?php

class AdminInvitation
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
            CREATE TABLE IF NOT EXISTS admin_invitations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                invitation_token VARCHAR(64) NOT NULL UNIQUE,
                is_used TINYINT(1) NOT NULL DEFAULT 0,
                used_at DATETIME DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function generateUniqueToken($length = 16)
    {
        do {
            $token = strtoupper(substr(bin2hex(random_bytes(20)), 0, $length));
        } while ($this->tokenExists($token));

        return $token;
    }

    private function tokenExists($token)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_invitations WHERE invitation_token = ?");
        $stmt->execute([$token]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_invitations WHERE email = ?");
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createInvitation($email)
    {
        $email = trim(mb_strtolower($email, 'UTF-8'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'adresse email est invalide.");
        }

        if ($this->emailExists($email)) {
            throw new InvalidArgumentException("Une invitation admin existe déjà pour cette adresse email.");
        }

        $token = $this->generateUniqueToken();

        $stmt = $this->pdo->prepare("
            INSERT INTO admin_invitations (email, invitation_token)
            VALUES (?, ?)
        ");
        $stmt->execute([$email, $token]);

        return [
            'id' => (int)$this->pdo->lastInsertId(),
            'email' => $email,
            'invitation_token' => $token,
        ];
    }

    public function getAllInvitations()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM admin_invitations
            ORDER BY created_at DESC, id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEmailAndToken($email, $token)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM admin_invitations
            WHERE email = ? AND invitation_token = ?
            LIMIT 1
        ");
        $stmt->execute([
            trim(mb_strtolower($email, 'UTF-8')),
            trim($token)
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAsUsed($id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE admin_invitations
            SET is_used = 1, used_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([(int)$id]);
    }

    public function deleteById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM admin_invitations WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}