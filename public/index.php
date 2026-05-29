<?php
session_start();

require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/QuizController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/AccessController.php';
require_once __DIR__ . '/../app/controllers/CandidateAccessController.php';
require_once __DIR__ . '/../app/controllers/AdminUsersController.php';

$page = $_GET['page'] ?? 'home';

$publicPages = [
    'access_login',
    'access_login_submit',
    'access_identity',
    'access_identity_submit',
    'admin_login',
    'admin_login_submit',
    'admin_activate',
    'admin_activate_submit'
];

if (!isset($_SESSION['user_id']) && !in_array($page, $publicPages, true)) {
    header('Location: index.php?page=access_login');
    exit();
}

switch ($page) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'admin_login':
        $controller = new AuthController();
        $controller->showAdminLogin();
        break;

    case 'admin_login_submit':
        $controller = new AuthController();
        $controller->adminLogin();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'instructions':
        $controller = new QuizController();
        $controller->instructions();
        break;

    case 'test_code':
        $controller = new QuizController();
        $controller->showTestCode();
        break;

    case 'test_code_submit':
        $controller = new QuizController();
        $controller->submitTestCode();
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
        $controller = new QuizController();
        $controller->result();
        break;

    case 'my_result':
        $controller = new QuizController();
        $controller->myResult();
        break;

    case 'admin_results':
        $controller = new AdminController();
        $controller->index();
        break;

    case 'admin_user_results':
        $controller = new AdminController();
        $controller->userResults();
        break;

    case 'admin_result_details':
        $controller = new AdminController();
        $controller->details();
        break;

    case 'admin_stats':
        $controller = new AdminController();
        $controller->stats();
        break;

    case 'quiz_preview':
        $controller = new AdminController();
        $controller->quizManager();
        break;

    case 'admin_create_question':
        $controller = new AdminController();
        $controller->createQuestion();
        break;

    case 'admin_update_question':
        $controller = new AdminController();
        $controller->updateQuestion();
        break;

    case 'admin_bulk_update_questions':
        $controller = new AdminController();
        $controller->bulkUpdateQuestions();
        break;

    case 'admin_delete_question':
        $controller = new AdminController();
        $controller->deleteQuestion();
        break;

    case 'admin_access_keys':
        $controller = new AccessController();
        $controller->index();
        break;

    case 'admin_create_access':
        $controller = new AccessController();
        $controller->create();
        break;

    case 'admin_delete_access':
        $controller = new AccessController();
        $controller->delete();
        break;

    case 'access_login':
        $controller = new CandidateAccessController();
        $controller->showLogin();
        break;

    case 'access_login_submit':
        $controller = new CandidateAccessController();
        $controller->login();
        break;

    case 'access_identity':
        $controller = new CandidateAccessController();
        $controller->showIdentity();
        break;

    case 'access_identity_submit':
        $controller = new CandidateAccessController();
        $controller->completeIdentity();
        break;

    case 'admin_users':
        $controller = new AdminUsersController();
        $controller->index();
        break;

    case 'admin_users_promote':
        $controller = new AdminUsersController();
        $controller->promote();
        break;

    case 'admin_users_demote':
        $controller = new AdminUsersController();
        $controller->demote();
        break;

    case 'admin_users_delete':
        $controller = new AdminUsersController();
        $controller->delete();
        break;

    case 'admin_users_invite_admin':
        $controller = new AdminUsersController();
        $controller->inviteAdmin();
        break;

    case 'admin_users_resend_invitation':
        $controller = new AdminUsersController();
        $controller->resendInvitation();
        break;

    case 'admin_users_delete_invitation':
        $controller = new AdminUsersController();
        $controller->deleteInvitation();
        break;

    case 'admin_activate':
        $controller = new AdminInviteController();
        $controller->showActivation();
        break;

    case 'admin_activate_submit':
        $controller = new AdminInviteController();
        $controller->activate();
        break;
    
    
    case 'admin_test_codes':
        $controller = new AdminController();
        $controller->testCodes();
        break;

    case 'admin_update_test_code':
        $controller = new AdminController();
        $controller->updateTestCode();
        break;

    case 'choose_test':
        $controller = new QuizController();
        $controller->chooseTest();
        break;
    
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
}