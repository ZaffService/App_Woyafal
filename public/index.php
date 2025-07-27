<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Place ce bloc ici, tout de suite après les headers !
if (isset($_GET['run_migration']) && $_GET['run_migration'] === 'secret') {
    require_once __DIR__ . '/../migrations/Migration.php';
    (new \App\Migrations\Migration())->run();
    echo "Migration exécutée";
    exit;
}

if (isset($_GET['run_seeder']) && $_GET['run_seeder'] === 'secret') {
    require_once __DIR__ . '/../seeders/Seeder.php';
    (new \App\Seeders\Seeder())->run();
    echo "Seeder exécuté";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$container = require_once __DIR__ . '/../bootstrap.php';

use App\Core\Container;

try {
    // Récupération de la route
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Nettoyage de l'URI
    $path = parse_url($requestUri, PHP_URL_PATH);
    $path = rtrim($path, '/');
    if (empty($path)) {
        $path = '/';
    }
    
    // Chargement des routes
    $routes = require_once __DIR__ . '/../routes/route.api.php';
    
    // Recherche de la route
    $routeFound = false;
    foreach ($routes as $routePath => $routeConfig) {
        // Gestion des routes avec paramètres
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $path, $matches) && in_array($requestMethod, $routeConfig['methods'])) {
            $routeFound = true;
            
            // Extraction des paramètres
            $params = [];
            if (preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames)) {
                for ($i = 1; $i < count($matches); $i++) {
                    $params[$paramNames[1][$i-1]] = $matches[$i];
                }
            }
            
            // Résolution du contrôleur via le container
            $controller = $container->resolve($routeConfig['controller']);
            $method = $routeConfig['method'];
            
            // Appel de la méthode
            if (empty($params)) {
                $controller->$method();
            } else {
                $controller->$method($params);
            }
            break;
        }
    }
    
    if (!$routeFound) {
        http_response_code(404);
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 404,
            'message' => 'Route non trouvée'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'data' => null,
        'statut' => 'error',
        'code' => 500,
        'message' => 'Erreur serveur: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString() // Ajoute ceci pour voir la source exacte
    ], JSON_UNESCAPED_UNICODE);
}
