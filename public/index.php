<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../config/routes.php';


use App\Helpers\PermissionMiddleware;
PermissionMiddleware::checkMaintenance();

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$routeFound = false;

foreach (
    $routes as $pattern => $route) {
    if ($pattern === '/' . $uri) {
        $controllerName = $route['controller'];
        $method = $route['action'];
        $controllerClass = "\\App\\Controllers\\$controllerName";
        $controller = new $controllerClass();
        $controller->$method();
        $routeFound = true;
        break;
    }
}

if (!$routeFound) {
    require_once __DIR__ . '/../Views/error/404.php';
} 