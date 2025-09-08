<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\DIContainer;
use Application\Controllers\LoginController;
use Application\Controllers\RegistrationController;
use Application\Controllers\ProfileController;
use Application\Controllers\SessionController;
use Application\ApplicationFactory;
use Application\Commands\LogoutCommand;
use Infrastructure\InfraFactory;
use Presentation\ErrorView;


function handleError(Throwable $e, bool $isAjax = false): string
{
    // Логируем ошибку
    error_log($e);

    // Определяем код и сообщение для пользователя
    $httpCode = 500;
    $userMessage = 'Произошла ошибка, попробуйте позже.';

    if ($e instanceof \InvalidArgumentException) {
        $httpCode = 400;
        $userMessage = 'Неверные данные в запросе.';
    } elseif ($e instanceof \RuntimeException && str_contains($e->getMessage(), 'Route not found')) {
        $httpCode = 404;
        $userMessage = 'Страница не найдена.';
    }

    // Устанавливаем HTTP-код
    if (!headers_sent()) {
        http_response_code($httpCode);
    }

    // Определяем окружение

    $errorDetails = ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];

    // Для AJAX возвращаем JSON
    if ($isAjax) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        return json_encode($errorDetails, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    // Для обычного запроса рендерим HTML
    $view = new ErrorView();
    $html = $view->render('error', [
        'errorMessage' => $userMessage,
        'httpCode' => $httpCode,
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    return $html ?: "<h1>Ошибка {$httpCode}</h1><p>{$userMessage}</p>";
}

$container = DIContainer::instance();

$dbFactory = new InfraFactory($container, $appConfig);
$appFactory = new ApplicationFactory($container);

$router = new Router($container);

$RegistrationController = $container->get(RegistrationController::class);
$LoginController = $container->get(LoginController::class);
$ProfileController = $container->get(ProfileController::class);
$SessionController = $container->get(SessionController::class);
$LogoutCommand = $container->get(LogoutCommand::class);


$router->get("/", [$LoginController, 'displayInfo'], false);
$router->post("/", [$LoginController, 'loginUser'], false, "profile/:id");
$router->get("/registration", [$RegistrationController, 'displayInfo'], false);
$router->post("/registration", [$RegistrationController, 'registerUser'], false, "profile/:id");


$router->get("/profile/:id", [$ProfileController, 'displayInfo'], true);
$router->get("/sessions/:id", [$SessionController, 'displayInfo'], true);

$router->delete("/logout", [$LogoutCommand, 'handle'], true, '/');


try {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $result = $router->dispatch($method, $uri);

        echo $result;
        exit;
    }
} catch (Throwable $e) {
    echo handleError($e, $isAjax ?? false);
    exit;
}