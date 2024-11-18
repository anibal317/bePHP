<?php
// src/UserController.php

require_once __DIR__ . '/../../config/database.php';
require_once 'Product.php';


class ProductController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    // Método para manejar la solicitud POST (Crear un producto)
    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->nombre) && !empty($data->descripcion)) {
            $this->product->nombre = $data->nombre;
            $this->product->descripcion = $data->descripcion;

            if ($this->product->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Producto creado exitosamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo crear el producto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos."]);
        }
    }

    // Método para manejar la solicitud GET (Leer productos)
    public function read($id = null) {
        if ($id !== null) {
            // Get a specific product
            $this->product->id = $id;
            $result = $this->product->readOne();

            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Producto no encontrado."]);
            }
        } else {
            // Get all products
            $stmt = $this->product->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $product_arr = [];
                $product_arr["registros"] = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $product_item = [
                        "id" => $row['id'],
                        "nombre" => $row['nombre'],
                        "descripcion" => $row['descripcion'],
                        "precio" => $row['precio'],
                        "creado_en" => $row['creado_en'],
                    ];
                    array_push($product_arr["registros"], $product_item);
                }

                http_response_code(200);
                echo json_encode($product_arr);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No se encontraron productos."]);
            }
        }
    }

    // Método para manejar la solicitud PUT (Actualizar producto)
    public function update() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && (!empty($data->nombre) || !empty($data->descripcion))) {
            $this->product->id = $data->id;
            $this->product->nombre = $data->nombre ?? null;
            $this->product->descripcion = $data->descripcion ?? null;

            if ($this->product->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Producto actualizado exitosamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo actualizar el producto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos o incorrectos."]);
        }
    }

    // Método para manejar la solicitud DELETE (Eliminar producto)
    public function delete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $this->product->id = $data->id;

            if ($this->product->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Producto eliminado exitosamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo eliminar el producto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos."]);
        }
    }
}