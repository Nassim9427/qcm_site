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
}