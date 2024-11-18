<?php
header("Access-Control-Allow-Origin: *"); // Permitir acceso desde cualquier origen
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Cabeceras permitidas
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../../src/products/ProductController.php';

$controller = new ProductController();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Remove any empty elements and re-index the array
$uri = array_values(array_filter($uri));

switch ($method) {
    case 'GET':
        if (isset($uri[2]) && is_numeric($uri[2])) {
            // GET request for a specific product
            $controller->read($uri[2]);
        } else {
            // GET request for all products
            $controller->read();
        }
        break;
    case 'POST':
        $controller->create();
        break;
    case 'PUT':
        $controller->update();
        break;
    case 'DELETE':
        $controller->delete();
        break;
    case 'OPTIONS':
        http_response_code(204); 
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}
