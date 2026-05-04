<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $pdo;
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->userModel = new User($this->pdo);
    }

    public function showAdminLogin()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) {
            header('Location: index.php?page=home');
            exit();
        }

        require __DIR__ . '/../views/admin_login.php';
    }

    public function adminLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_login');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        try {
            if ($email === '' || $motDePasse === '') {
                throw new RuntimeException("Veuillez remplir tous les champs.");
            }

            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                throw new RuntimeException("Identifiants invalides.");
            }

            if ((int)($user['is_admin'] ?? 0) !== 1) {
                throw new RuntimeException("Accès réservé aux administrateurs.");
            }

            $hash = $user['mot_de_passe'] ?? '';

            if ($hash === '' || !password_verify($motDePasse, $hash)) {
                throw new RuntimeException("Identifiants invalides.");
            }

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['nom'] = $user['nom'] ?? '';
            $_SESSION['prenom'] = $user['prenom'] ?? '';
            $_SESSION['email'] = $user['email'] ?? '';
            $_SESSION['is_admin'] = 1;

            unset($_SESSION['pending_access_id']);
            unset($_SESSION['access_id']);

            header('Location: index.php?page=home');
            exit();
        } catch (Throwable $e) {
            $_SESSION['admin_login_error'] = $e->getMessage();
            header('Location: index.php?page=admin_login');
            exit();
        }
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?page=access_login');
        exit();
    }
}