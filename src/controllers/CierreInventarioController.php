<?php

require_once '../repositories/CierreInventarioRepository.php';

class CierreInventarioController {
    private $repository;

    public function __construct($db) {
        $this->repository = new CierreInventarioRepository($db);
    }

    public function getAll() {
        $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $stmt = $this->repository->getAll($fechaInicio, $fechaFin);
        $num = $stmt->rowCount();

        if($num > 0) {
            $cierres_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $cierre_item = array(
                    "id_cierre_inventario" => $id_cierre_invetarios,
                    "fk_id_producto" => $fk_id_producto,
                    "fecha" => $fecha,
                    "cantidad" => $cantidad
                );
                array_push($cierres_arr, $cierre_item);
            }

            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierres de inventario obtenidos correctamente",
                "data" => $cierres_arr
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "No se encontraron cierres de inventario",
                "data" => array()
            ));
        }
    }

    public function getById($id) {
        $cierre = $this->repository->getById($id);

        if($cierre->id_cierre_invetarios != null) {
            $cierre_arr = array(
                "id_cierre_inventario" => $cierre->id_cierre_invetarios,
                "fk_id_producto" => $cierre->fk_id_producto,
                "fecha" => $cierre->fecha,
                "cantidad" => $cierre->cantidad
            );

            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierre de inventario obtenido correctamente",
                "data" => $cierre_arr
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "Cierre de inventario no encontrado",
                "data" => null
            ));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->create((array)$data)) {
                http_response_code(201);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Cierre de inventario creado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al crear el cierre de inventario",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para crear el cierre de inventario",
                "data" => null
            ));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->update($id, (array)$data)) {
                http_response_code(200);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Cierre de inventario actualizado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al actualizar el cierre de inventario",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para actualizar el cierre de inventario",
                "data" => null
            ));
        }
    }

    public function delete($id) {
        if($this->repository->delete($id)) {
            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierre de inventario eliminado correctamente",
                "data" => null
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "state" => 0,
                "message" => "Error al eliminar el cierre de inventario",
                "data" => null
            ));
        }
    }
}
?>
