<?php

require_once '../models/CierreInventario.php';

class CierreInventarioRepository {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $cierre = new CierreInventario($this->conn);
        return $cierre->read();
    }

    public function getById($id) {
        $cierre = new CierreInventario($this->conn);
        $cierre->id_cierre_invetarios = $id;
        $cierre->readOne();
        return $cierre;
    }

    public function create($data) {
        $cierre = new CierreInventario($this->conn);
        $cierre->fk_id_producto = $data['fk_id_producto'];
        $cierre->fecha = $data['fecha'];
        $cierre->cantidad = $data['cantidad'];
        return $cierre->create();
    }

    public function update($id, $data) {
        $cierre = new CierreInventario($this->conn);
        $cierre->id_cierre_invetarios = $id;
        $cierre->fk_id_producto = $data['fk_id_producto'];
        $cierre->fecha = $data['fecha'];
        $cierre->cantidad = $data['cantidad'];
        return $cierre->update();
    }

    public function delete($id) {
        $cierre = new CierreInventario($this->conn);
        $cierre->id_cierre_invetarios = $id;
        return $cierre->delete();
    }
}
?>
