<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/AdminInvitation.php';

class AdminUsersController
{
    private $pdo;
    private $userModel;
    private $invitationModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
            header('Location: index.php?page=home');
            exit();
        }

        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->userModel = new User($this->pdo);
        $this->invitationModel = new AdminInvitation($this->pdo);
    }

    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');

        $users = $this->userModel->getAllWithQuizStatus($search, $role);
        $invitations = $this->invitationModel->getAllInvitations();

        require __DIR__ . '/../views/admin_users.php';
    }

    public function promote()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        try {
            $user = $this->userModel->findById($userId);

            if (!$user) {
                throw new RuntimeException("Utilisateur introuvable.");
            }

            if ((int)($user['is_admin'] ?? 0) === 1) {
                throw new RuntimeException("Cet utilisateur est déjà administrateur.");
            }

            $this->userModel->promoteToAdmin($userId);
            $_SESSION['admin_users_success'] = "Utilisateur promu administrateur.";
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    public function demote()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        try {
            if ($userId === (int)$_SESSION['user_id']) {
                throw new RuntimeException("Vous ne pouvez pas vous rétrograder vous-même.");
            }

            $user = $this->userModel->findById($userId);

            if (!$user) {
                throw new RuntimeException("Utilisateur introuvable.");
            }

            if ((int)($user['is_admin'] ?? 0) !== 1) {
                throw new RuntimeException("Cet utilisateur n'est pas administrateur.");
            }

            $this->userModel->demoteFromAdmin($userId);
            $_SESSION['admin_users_success'] = "Administrateur rétrogradé en utilisateur.";
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        try {
            if ($userId === (int)$_SESSION['user_id']) {
                throw new RuntimeException("Vous ne pouvez pas supprimer votre propre compte.");
            }

            $user = $this->userModel->findById($userId);

            if (!$user) {
                throw new RuntimeException("Utilisateur introuvable.");
            }

            $this->userModel->deleteById($userId);
            $_SESSION['admin_users_success'] = "Utilisateur supprimé.";
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    public function inviteAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        try {
            $email = trim(mb_strtolower($_POST['email'] ?? '', 'UTF-8'));

            if ($email === '') {
                throw new RuntimeException("Veuillez renseigner un email.");
            }

            $existingUser = $this->userModel->findByEmail($email);

            if ($existingUser && (int)($existingUser['is_admin'] ?? 0) === 1) {
                throw new RuntimeException("Cet email appartient déjà à un administrateur.");
            }

            $created = $this->invitationModel->createInvitation($email);

            $mailSent = $this->sendAdminInvitationEmail(
                $created['email'],
                $created['invitation_token']
            );

            if ($mailSent) {
                $_SESSION['admin_users_success'] =
                    "Invitation admin envoyée par email à " . $created['email'];
            } else {
                $_SESSION['admin_users_error'] =
                    "Invitation créée mais email non envoyé. Token : " . $created['invitation_token'];
            }
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    public function resendInvitation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            $invitations = $this->invitationModel->getAllInvitations();
            $targetInvitation = null;

            foreach ($invitations as $invitation) {
                if ((int)$invitation['id'] === $id) {
                    $targetInvitation = $invitation;
                    break;
                }
            }

            if (!$targetInvitation) {
                throw new RuntimeException("Invitation introuvable.");
            }

            if (!empty($targetInvitation['is_used'])) {
                throw new RuntimeException("Cette invitation a déjà été utilisée.");
            }

            $mailSent = $this->sendAdminInvitationEmail(
                $targetInvitation['email'],
                $targetInvitation['invitation_token']
            );

            if ($mailSent) {
                $_SESSION['admin_users_success'] =
                    "Invitation admin renvoyée à " . $targetInvitation['email'];
            } else {
                $_SESSION['admin_users_error'] =
                    "Impossible d'envoyer l'email. Token : " . $targetInvitation['invitation_token'];
            }
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    public function deleteInvitation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_users');
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            if ($id <= 0) {
                throw new RuntimeException("Invitation introuvable.");
            }

            $this->invitationModel->deleteById($id);
            $_SESSION['admin_users_success'] = "Invitation admin supprimée.";
        } catch (Throwable $e) {
            $_SESSION['admin_users_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_users');
        exit();
    }

    private function sendAdminInvitationEmail($email, $token)
    {
        $subject = 'Invitation administrateur - ITEAM QUALITY';
        $activationLink = APP_URL . '/index.php?page=admin_activate';

        $message = "Bonjour,\r\n\r\n";
        $message .= "Vous avez été invité à créer un compte administrateur sur ITEAM QUALITY.\r\n\r\n";
        $message .= "Voici vos informations d'activation :\r\n\r\n";
        $message .= "Email : " . $email . "\r\n";
        $message .= "Token d'invitation : " . $token . "\r\n\r\n";
        $message .= "Lien d'activation :\r\n";
        $message .= $activationLink . "\r\n\r\n";
        $message .= "Lors de l'activation, vous devrez renseigner votre nom, votre prénom et choisir votre mot de passe.\r\n\r\n";
        $message .= "Attention : ce token est à usage unique.\r\n\r\n";
        $message .= "Bonne configuration !\r\n";

        $headers = [];
        $headers[] = 'From: noreply@qcm.com';
        $headers[] = 'Reply-To: noreply@qcm.com';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';

        return mail($email, $subject, $message, implode("\r\n", $headers));
    }
}