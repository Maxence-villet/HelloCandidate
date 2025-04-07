<?php
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/register':
        require __DIR__ . '/routes/register.php';
        break;
    case '/login':
        require __DIR__ . '/routes/login.php';
        break;
    case '/welcome':
        require __DIR__ . '/routes/welcome.php';
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

    case '/notifications':
        require __DIR__ . '/controllers/NotificationsController.php';
        $controller = new NotificationsController();
        $controller->listNotifications();
        break;
            
    default:
        http_response_code(404);
        echo "Page non trouvée";
        break;
}