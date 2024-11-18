<?php
// public/index.php

require_once __DIR__ . '/../src/products/ProductController.php';

// Obtener la URI de la solicitud
$request_uri = $_SERVER['REQUEST_URI'];

// Ajusta esto según tu configuración
$base_path = '/bePhp/public';

// Eliminar el base_path de la URI
$request_uri = substr($request_uri, strlen($base_path));

// Dividir la URI en segmentos
$segments = explode('/', trim($request_uri, '/'));

// Crear una instancia del controlador
$controller = new ProductController();

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Manejar las rutas
if (empty($segments[0])) {
    // Ruta raíz
    echo json_encode(["message" => "Bienvenido a la API de productos"]);
} elseif ($segments[0] === 'products') {
    switch ($method) {
        case 'GET':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $controller->read($segments[1]);
            } else {
                $controller->read();
            }
            break;
        case 'POST':
            $controller->create();
            break;
        case 'PUT':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $controller->update($segments[1]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "ID de producto no proporcionado"]);
            }
            break;
        case 'DELETE':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $controller->delete($segments[1]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "ID de producto no proporcionado"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Ruta no encontrada"]);
}