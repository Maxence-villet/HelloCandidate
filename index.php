<?php
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/':
        require __DIR__ . '/routes/index.php';
        break;
    case '/register':
        require __DIR__ . '/routes/register.php';
        break;
    case '/login':
        require __DIR__ . '/routes/login.php';
        break;
    case '/student/dashboard':
        require __DIR__ . '/routes/student/dashboard.php';
        break;
    case '/dashboard':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        echo "Bienvenue, " . htmlspecialchars($_SESSION['username']) . " ! Ceci est votre tableau de bord.";
        break;
    case '/applications':
        require __DIR__ . '/controllers/ApplicationController.php';
        $controller = new ApplicationController();
        $controller->listApplications();
        break;
    case '/applications/add':
        require __DIR__ . '/controllers/ApplicationController.php';
        $controller = new ApplicationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->addApplication();
        } else {
            $controller->showApplicationForm();
        }
        break;
    case '/applications/view':
        require __DIR__ . '/controllers/ApplicationController.php';
        $controller = new ApplicationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
            $controller->viewApplication($_POST['application_id']);
        } else {
            http_response_code(400);
            header('Location: /student/dashboard');
        }
        break;
    case '/applications/update-status':
        require __DIR__ . '/controllers/ApplicationController.php';
        $controller = new ApplicationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateApplicationStatus();
        } else {
            http_response_code(405);
            echo "Méthode non autorisée";
        }
        break;
    case '/rankings':
        require __DIR__ . '/controllers/RankingController.php';
        $controller = new RankingController();
        $controller->showRanking();
        break;
    case '/notifications':
        require __DIR__ . '/controllers/NotificationsController.php';
        $controller = new NotificationsController();
        $controller->listNotifications();
        break;
    case '/notifications/handle':
        require __DIR__ . '/controllers/NotificationsController.php';
        $controller = new NotificationsController();
        $controller->handleInvitation();
        break;
    case '/logout':
        require __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
    case '/spectator/register':
        require __DIR__ . '/routes/spectator/register.php';
        break;
    case '/spectator/dashboard':
        $_SESSION['manage_group_id'] = null;
        require __DIR__ . '/routes/spectator/dashboard.php';
        break;
    case '/group/create':
        require __DIR__ . '/routes/group/create.php';
        break;
    case '/group/manage':
        require __DIR__ . '/routes/group/manage.php';
        break;
    case '/group/add':
        require __DIR__ . '/routes/group/add.php';
        break;
    case '/group/remove':
        require __DIR__ . '/routes/group/remove.php';
        break;
    case '/profile/stats':
        require __DIR__ . '/routes/profile/stats.php';
        break;
    case '/profile':
        require __DIR__ . '/routes/profile/profile.php';
        break;
    case '/help':
        session_start();
        require __DIR__ . '/routes/help.php';
        break;
    default:
        http_response_code(404);
        echo "Page non trouvée";
        break;
}