<?php
session_start();

require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/QuizController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

$page = $_GET['page'] ?? 'home';

/* Pages autorisées sans connexion */
$publicPages = ['login', 'register'];

/* Protection globale */
if (!isset($_SESSION['user_id']) && !in_array($page, $publicPages, true)) {
    header('Location: index.php?page=login');
    exit();
}

switch ($page) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'instructions':
        $controller = new QuizController();
        $controller->instructions();
        break;

    case 'quiz':
        $controller = new QuizController();
        $controller->start();
        break;

    case 'submit_quiz':
        $controller = new QuizController();
        $controller->submit();
        break;

    case 'result':
        require_once __DIR__ . '/../app/views/result.php';
        break;

    case 'admin_results':
        $controller = new AdminController();
        $controller->index();
        break;

    case 'admin_result_details':
        $controller = new AdminController();
        $controller->details();
        break;

    case 'admin_stats':
        $controller = new AdminController();
        $controller->stats();
        break;

    case 'my_result':
        $controller = new QuizController();
        $controller->myResult();
        break;
        
    case 'quiz_preview':
        $controller = new QuizController();
        $controller->preview();
        break;

    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
}