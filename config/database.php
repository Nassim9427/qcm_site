<?php

class Database
{
    private $host = '127.0.0.1';
    private $port = '3308';
    private $dbname = 'qcm_site';
    private $username = 'root';
    private $password = '';

    public function getConnection()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}