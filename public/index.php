<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    
    $errorMessage = date('[d/m/Y H:i:s] ') . "Erreur PHP: $message dans $file ligne $line\n";
    file_put_contents(__DIR__ . '/../storage/logs/php_errors.log', $errorMessage, FILE_APPEND);
    
    
    if ($severity === E_ERROR || $severity === E_PARSE || $severity === E_CORE_ERROR) {
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur serveur interne']);
            exit;
        }
    }
});

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

foreach ($routes as $pattern => $route) {
    
    $regex = '#^' . str_replace('/', '\/', $pattern) . '$#';
    
    if (preg_match($regex, '/' . $uri, $matches)) {
        $controllerName = $route['controller'];
        $method = $route['action'];
        $controllerClass = "\\App\\Controllers\\$controllerName";
        $controller = new $controllerClass();
        
        
        if (count($matches) > 1) {
            array_shift($matches); 
            $controller->$method(...$matches);
        } else {
            $controller->$method();
        }
        
        $routeFound = true;
        break;
    }
}

if (!$routeFound) {
    require_once __DIR__ . '/../Views/error/404.php';
} 