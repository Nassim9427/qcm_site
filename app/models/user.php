<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $prenom, $email, $telephone, $entreprise, $poste, $motDePasseHash)
    {
        $sql = "INSERT INTO users (nom, prenom, email, telephone, entreprise, poste, mot_de_passe)
                VALUES (:nom, :prenom, :email, :telephone, :entreprise, :poste, :mot_de_passe)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone ?: null,
            'entreprise' => $entreprise ?: null,
            'poste' => $poste ?: null,
            'mot_de_passe' => $motDePasseHash
        ]);
    }

    public function createAdmin($nom, $prenom, $email, $motDePasseHash)
    {
        $sql = "INSERT INTO users (nom, prenom, email, telephone, entreprise, poste, mot_de_passe, is_admin)
                VALUES (:nom, :prenom, :email, :telephone, :entreprise, :poste, :mot_de_passe, :is_admin)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => null,
            'entreprise' => null,
            'poste' => null,
            'mot_de_passe' => $motDePasseHash,
            'is_admin' => 1
        ]);
    }

    public function promoteToAdmin($userId)
    {
        $sql = "UPDATE users SET is_admin = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => (int)$userId]);
    }

    public function demoteFromAdmin($userId)
    {
        $sql = "UPDATE users SET is_admin = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => (int)$userId]);
    }

    public function deleteById($userId)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => (int)$userId]);
    }

    public function getAllWithQuizStatus($search = '', $role = '')
    {
        $sql = "
            SELECT
                u.*,
                qr.id AS quiz_result_id,
                qr.score,
                qr.total_questions,
                qr.created_at AS quiz_completed_at
            FROM users u
            LEFT JOIN quiz_results qr ON qr.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (
                u.nom LIKE :search
                OR u.prenom LIKE :search
                OR u.email LIKE :search
            )";
            $params['search'] = '%' . $search . '%';
        }

        if ($role === 'admin') {
            $sql .= " AND u.is_admin = 1";
        } elseif ($role === 'user') {
            $sql .= " AND (u.is_admin = 0 OR u.is_admin IS NULL)";
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}