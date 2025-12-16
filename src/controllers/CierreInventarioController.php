<?php

require_once '../repositories/CierreInventarioRepository.php';

class CierreInventarioController {
    private $repository;

    public function __construct($db) {
        $this->repository = new CierreInventarioRepository($db);
    }

    public function getAll() {
        $stmt = $this->repository->getAll();
        $num = $stmt->rowCount();

        if($num > 0) {
            $cierres_arr = array();
            $cierres_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $cierre_item = array(
                    "id_cierre_invetarios" => $id_cierre_invetarios,
                    "fk_id_producto" => $fk_id_producto,
                    "fecha" => $fecha,
                    "cantidad" => $cantidad
                );
                array_push($cierres_arr["records"], $cierre_item);
            }

            http_response_code(200);
            echo json_encode($cierres_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No cierres found."));
        }
    }

    public function getById($id) {
        $cierre = $this->repository->getById($id);

        if($cierre->id_cierre_invetarios != null) {
            $cierre_arr = array(
                "id_cierre_invetarios" => $cierre->id_cierre_invetarios,
                "fk_id_producto" => $cierre->fk_id_producto,
                "fecha" => $cierre->fecha,
                "cantidad" => $cierre->cantidad
            );

            http_response_code(200);
            echo json_encode($cierre_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Cierre not found."));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->create((array)$data)) {
                http_response_code(201);
                echo json_encode(array("message" => "Cierre was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create cierre."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create cierre. Data is incomplete."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->update($id, (array)$data)) {
                http_response_code(200);
                echo json_encode(array("message" => "Cierre was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update cierre."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update cierre. Data is incomplete."));
        }
    }

    public function delete($id) {
        if($this->repository->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "Cierre was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete cierre."));
        }
    }
}
?>
