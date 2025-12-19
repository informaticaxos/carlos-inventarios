<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';

class ProductController
{
    private $productRepository;

    public function __construct($pdo)
    {
        $this->productRepository = new ProductRepository($pdo);
    }

    // GET /producto
    public function getAll()
    {
        // El repositorio actual devuelve todos, se podría implementar paginación en el repo después
        $data = $this->productRepository->findAll();
        echo json_encode([
            "state" => 1,
            "data" => $data
        ]);
    }

    // GET /producto/{id}
    public function getById($id)
    {
        $data = $this->productRepository->findById($id);
        if ($data) {
            echo json_encode([
                "state" => 1,
                "data" => $data
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "state" => 0,
                "message" => "Producto no encontrado"
            ]);
        }
    }

    // POST /producto
    public function create()
    {
        // Leer el JSON enviado en el cuerpo de la petición
        $input = json_decode(file_get_contents('php://input'), true);

        // Validar campos mínimos requeridos según tu modelo (brand, code, etc.)
        if (isset($input['code']) && isset($input['brand'])) {
            $product = new Product();
            $product->setBrand($input['brand'] ?? '');
            $product->setDescription($input['description'] ?? '');
            $product->setStock($input['stock'] ?? 0);
            $product->setCost($input['cost'] ?? 0.0);
            $product->setPvp($input['pvp'] ?? 0.0);
            $product->setMin($input['min'] ?? 0);
            $product->setCode($input['code']);
            $product->setPercha($input['percha'] ?? '');
            // aux se genera automáticamente en el repositorio si no se envía

            $this->productRepository->save($product);

            http_response_code(201);
            echo json_encode([
                "state" => 1,
                "message" => "Producto creado exitosamente",
                "id" => $product->getIdProduct()
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "state" => 0,
                "message" => "Datos incompletos. Se requiere al menos 'code' y 'brand'"
            ]);
        }
    }

    // PUT /producto/{id}
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Verificar si existe
        $existingData = $this->productRepository->findById($id);
        if (!$existingData) {
            http_response_code(404);
            echo json_encode(["state" => 0, "message" => "Producto no encontrado"]);
            return;
        }

        try {
            // Crear objeto con datos existentes y sobrescribir con los nuevos
            $product = new Product($id, 
                $input['brand'] ?? $existingData['brand'],
                $input['description'] ?? $existingData['description'],
                $input['stock'] ?? $existingData['stock'],
                $input['cost'] ?? $existingData['cost'],
                $input['pvp'] ?? $existingData['pvp'],
                $input['min'] ?? $existingData['min'],
                $input['code'] ?? $existingData['code'],
                $input['aux'] ?? $existingData['aux'],
                $input['percha'] ?? $existingData['percha']
            );

            $this->productRepository->save($product);
            echo json_encode(["state" => 1, "message" => "Producto actualizado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "Error al actualizar: " . $e->getMessage()]);
        }
    }

    // DELETE /producto/{id}
    public function delete($id)
    {
        // Verificar si existe antes de borrar
        if (!$this->productRepository->findById($id)) {
            http_response_code(404);
            echo json_encode(["state" => 0, "message" => "Producto no encontrado"]);
            return;
        }

        try {
            $this->productRepository->delete($id);
            echo json_encode(["state" => 1, "message" => "Producto eliminado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "Error al eliminar"]);
        }
    }
}