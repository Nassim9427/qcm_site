<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/AccessKey.php';

class CandidateAccessController
{
    private $accessKeyModel;

    public function __construct()
    {
        $database = new Database();
        $pdo = $database->getConnection();
        $this->accessKeyModel = new AccessKey($pdo);
    }

    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit();
        }

        require __DIR__ . '/../views/access_login.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=access_login');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $accessKey = trim($_POST['access_key'] ?? '');

        try {
            $access = $this->accessKeyModel->getByEmailAndKey($email, $accessKey);

            if (!$access) {
                throw new RuntimeException("Email ou clé invalide.");
            }

            /*
             * CAS 1 : clé déjà utilisée
             * On autorise la connexion au site, mais l'utilisateur ne pourra plus refaire le quiz.
             */
            if (!empty($access['is_used'])) {
                if (empty($access['nom']) || empty($access['prenom'])) {
                    throw new RuntimeException("Cette clé a déjà été utilisée, mais les informations du candidat sont incomplètes.");
                }

                $user = $this->accessKeyModel->findOrCreateUserFromAccess($access);

                if (!$user) {
                    throw new RuntimeException("Impossible de recréer la session utilisateur.");
                }

                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = (int)($user['is_admin'] ?? 0);

                // Une clé déjà utilisée ne doit évidemment pas être reconduite pour un nouveau quiz
                unset($_SESSION['access_id']);
                unset($_SESSION['pending_access_id']);

                header('Location: index.php?page=home');
                exit();
            }

            /*
             * CAS 2 : clé encore non utilisée
             * On passe à l'étape identité.
             */
            $_SESSION['pending_access_id'] = (int)$access['id'];

            header('Location: index.php?page=access_identity');
            exit();
        } catch (Throwable $e) {
            $_SESSION['access_error'] = $e->getMessage();
            header('Location: index.php?page=access_login');
            exit();
        }
    }

    public function showIdentity()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit();
        }

        if (empty($_SESSION['pending_access_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $access = $this->accessKeyModel->getById((int)$_SESSION['pending_access_id']);

        if (!$access) {
            unset($_SESSION['pending_access_id']);
            $_SESSION['access_error'] = "Accès invalide.";
            header('Location: index.php?page=access_login');
            exit();
        }

        if (!empty($access['is_used'])) {
            unset($_SESSION['pending_access_id']);
            $_SESSION['access_error'] = "Cette clé a déjà été utilisée.";
            header('Location: index.php?page=access_login');
            exit();
        }

        require __DIR__ . '/../views/access_identity.php';
    }

    public function completeIdentity()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=access_login');
            exit();
        }

        if (empty($_SESSION['pending_access_id'])) {
            header('Location: index.php?page=access_login');
            exit();
        }

        $accessId = (int)$_SESSION['pending_access_id'];
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');

        try {
            $access = $this->accessKeyModel->getById($accessId);

            if (!$access) {
                throw new RuntimeException("Accès invalide.");
            }

            if (!empty($access['is_used'])) {
                throw new RuntimeException("Cette clé a déjà été utilisée.");
            }

            $this->accessKeyModel->completeIdentityAndMarkUsed($accessId, $nom, $prenom);

            $updatedAccess = $this->accessKeyModel->getById($accessId);
            $user = $this->accessKeyModel->findOrCreateUserFromAccess($updatedAccess);

            if (!$user) {
                throw new RuntimeException("Impossible de créer la session utilisateur.");
            }

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = (int)($user['is_admin'] ?? 0);

            // On garde l'id d'accès pour consommer la clé seulement à l'envoi du quiz
            $_SESSION['access_id'] = $accessId;

            unset($_SESSION['pending_access_id']);

            header('Location: index.php?page=home');
            exit();
        } catch (Throwable $e) {
            $_SESSION['access_identity_error'] = $e->getMessage();
            header('Location: index.php?page=access_identity');
            exit();
        }
    }
}