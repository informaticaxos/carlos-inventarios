<?php

require_once '../repositories/ProductoRepository.php';

class ProductoController {
    private $repository;

    public function __construct($db) {
        $this->repository = new ProductoRepository($db);
    }

    public function getAll() {
        $stmt = $this->repository->getAll();
        $num = $stmt->rowCount();

        if($num > 0) {
            $productos_arr = array();
            $productos_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $producto_item = array(
                    "id_producto" => $id_producto,
                    "nombre_producto" => $nombre_producto,
                    "unidad_medida_producto" => $unidad_medida_producto,
                    "categoria_producto" => $categoria_producto
                );
                array_push($productos_arr["records"], $producto_item);
            }

            http_response_code(200);
            echo json_encode($productos_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No productos found."));
        }
    }

    public function getById($id) {
        $producto = $this->repository->getById($id);

        if($producto->id_producto != null) {
            $producto_arr = array(
                "id_producto" => $producto->id_producto,
                "nombre_producto" => $producto->nombre_producto,
                "unidad_medida_producto" => $producto->unidad_medida_producto,
                "categoria_producto" => $producto->categoria_producto
            );

            http_response_code(200);
            echo json_encode($producto_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Producto not found."));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nombre_producto) && !empty($data->unidad_medida_producto) && !empty($data->categoria_producto)) {
            if($this->repository->create((array)$data)) {
                http_response_code(201);
                echo json_encode(array("message" => "Producto was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create producto. Data is incomplete."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nombre_producto) && !empty($data->unidad_medida_producto) && !empty($data->categoria_producto)) {
            if($this->repository->update($id, (array)$data)) {
                http_response_code(200);
                echo json_encode(array("message" => "Producto was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update producto. Data is incomplete."));
        }
    }

    public function delete($id) {
        if($this->repository->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "Producto was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete producto."));
        }
    }
}
?>
