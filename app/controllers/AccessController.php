<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../models/AccessKey.php';

class AccessController
{
    private $accessKeyModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
            header('Location: index.php?page=home');
            exit();
        }

        $database = new Database();
        $pdo = $database->getConnection();
        $this->accessKeyModel = new AccessKey($pdo);
    }

    public function index()
    {
        $accesses = $this->accessKeyModel->getAllAccesses();
        require __DIR__ . '/../views/admin_access_keys.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_access_keys');
            exit();
        }

        try {
            $created = $this->accessKeyModel->createAccess($_POST['email'] ?? '');

            $mailSent = $this->sendAccessEmail($created['email'], $created['access_key']);

            if ($mailSent) {
                $_SESSION['admin_access_success'] =
                    "Clé envoyée par email à " . $created['email'];
            } else {
                $_SESSION['admin_access_error'] =
                    "Clé générée mais email non envoyé. Clé : " . $created['access_key'];
            }
        } catch (Throwable $e) {
            $_SESSION['admin_access_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_access_keys');
        exit();
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_access_keys');
            exit();
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_access_error'] = "Accès introuvable.";
            header('Location: index.php?page=admin_access_keys');
            exit();
        }

        try {
            $deleted = $this->accessKeyModel->deleteById($id);

            if ($deleted) {
                $_SESSION['admin_access_success'] = "Accès supprimé avec succès.";
            } else {
                $_SESSION['admin_access_error'] = "Impossible de supprimer cet accès.";
            }
        } catch (Throwable $e) {
            $_SESSION['admin_access_error'] = $e->getMessage();
        }

        header('Location: index.php?page=admin_access_keys');
        exit();
    }

    private function sendAccessEmail($email, $accessKey)
    {
        $subject = 'Accès au test QA';
        $accessLink = APP_URL . '/index.php?page=access_login';

        $message = "Bonjour,\r\n\r\n";
        $message .= "Voici votre accès au test :\r\n\r\n";
        $message .= "Email : " . $email . "\r\n";
        $message .= "Clé : " . $accessKey . "\r\n\r\n";
        $message .= "Lien :\r\n";
        $message .= $accessLink . "\r\n\r\n";
        $message .= "Attention : la clé est utilisable une seule fois.\r\n\r\n";
        $message .= "Bonne chance !\r\n";

        $headers = [];
        $headers[] = 'From: noreply@qcm.com';
        $headers[] = 'Reply-To: noreply@qcm.com';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';

        return mail($email, $subject, $message, implode("\r\n", $headers));
    }
}