<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controller\FormController;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$router = new Router();
$formController = new FormController();

$router->get('/admin/menu', [$formController, 'adminMenu']);
$router->get('/user/reports', [$formController, 'userReports']);


$router->get('/register', [$formController, 'registerForm']);
$router->post('/register', [$formController, 'register']);

$router->get('/login', [$formController, 'loginForm']);
$router->post('/login', [$formController, 'login']);


$router->get('/', function () use ($formController) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $formController->index();
});

$router->post('/submit', function () use ($formController) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo 'Доступ запрещен';
        exit;
    }
    $formController->store();
});

$router->get('/export/pdf', [$formController, 'exportPdf']);
$router->get('/export/csv', [$formController, 'exportCsv']);
$router->get('/export/xlsx', [$formController, 'exportXlsx']);


$router->get('/logout', [$formController, 'logout']);

$router->resolve();
?>