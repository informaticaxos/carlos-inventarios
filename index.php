<?php

// Configuración de CORS (Permitir peticiones desde cualquier origen)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar peticiones OPTIONS (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- CONFIGURACIÓN DE BASE DE DATOS ---
require_once __DIR__ . '/../src/config/Database.php';

$database = new Database();
$pdo = $database->getConnection();

// --- CARGA DE DEPENDENCIAS ---
// Ajustamos las rutas para apuntar a la carpeta src
require_once __DIR__ . '/../src/controllers/ProductController.php';
require_once __DIR__ . '/../src/routes/ProductRoute.php';

// --- ENRUTADOR (ROUTER) ---

// Obtener la URL solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$dirname = dirname($scriptName);

// Normalizar directorios (Windows usa \, web usa /)
$dirname = str_replace('\\', '/', $dirname);

// Calcular la ruta relativa
$requestUri = explode('?', $requestUri)[0]; // Quitar parámetros GET

if (strpos($requestUri, '/api/') !== false) {
    // Si la URL contiene '/api/', tomamos todo lo que viene después
    $path = '/' . explode('/api/', $requestUri, 2)[1];
} else {
    // Fallback: intentar restar el directorio del script
    $path = str_replace($dirname, '', $requestUri);
}

// Asegurar formato correcto de la ruta
if (empty($path) || $path === '/') {
    $path = '/';
} else if (strpos($path, '/') !== 0) {
    $path = '/' . $path;
}

$method = $_SERVER['REQUEST_METHOD'];
$matched = false;

// Buscar coincidencia en las rutas definidas
foreach ($routes as $routeDef => $handler) {
    list($routeMethod, $routePath) = explode(' ', $routeDef, 2);
    
    if ($method !== $routeMethod) continue;

    // Convertir parámetros {id} a expresiones regulares
    $pattern = "@^" . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routePath) . "$@";

    if (preg_match($pattern, $path, $matches)) {
        array_shift($matches); // Eliminar la coincidencia completa, dejar solo los parámetros
        $controller = new $handler[0](); // Instanciar ProductController
        call_user_func_array([$controller, $handler[1]], $matches); // Llamar al método
        $matched = true;
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo json_encode([
        "state" => 0,
        "message" => "Endpoint no encontrado",
        "data" => ["path_recibido" => $path]
    ]);
}