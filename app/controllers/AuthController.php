<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->getConnection();
        $this->userModel = new User($pdo);
    }

    public function login()
    {
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            $old['email'] = $email;

            if (empty($email)) {
                $errors[] = "L'adresse email est obligatoire.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email n'est pas valide.";
            }

            if (empty($motDePasse)) {
                $errors[] = "Le mot de passe est obligatoire.";
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
                    $errors[] = "Email ou mot de passe incorrect.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

                    header('Location: index.php?page=home');
                    exit();
                }
            }
        }

        require __DIR__ . '/../views/login.php';
    }

    public function register()
    {
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $entreprise = trim($_POST['entreprise'] ?? '');
            $poste = trim($_POST['poste'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'] ?? '';

            $old = [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'entreprise' => $entreprise,
                'poste' => $poste,
            ];

            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }

            if (empty($prenom)) {
                $errors[] = "Le prénom est obligatoire.";
            }

            if (empty($email)) {
                $errors[] = "L'adresse email est obligatoire.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email n'est pas valide.";
            }

            if (empty($motDePasse)) {
                $errors[] = "Le mot de passe est obligatoire.";
            } elseif (strlen($motDePasse) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            }

            if ($motDePasse !== $confirmationMotDePasse) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $existingUser = $this->userModel->findByEmail($email);
                if ($existingUser) {
                    $errors[] = "Un compte existe déjà avec cette adresse email.";
                }
            }

            if (empty($errors)) {
                $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

                $success = $this->userModel->create(
                    $nom,
                    $prenom,
                    $email,
                    $telephone,
                    $entreprise,
                    $poste,
                    $motDePasseHash
                );

                if ($success) {
                    $user = $this->userModel->findByEmail($email);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

                    header('Location: index.php?page=home');
                    exit();
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription.";
                }
            }
        }

        require __DIR__ . '/../views/register.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: index.php?page=login');
        exit();
    }
}