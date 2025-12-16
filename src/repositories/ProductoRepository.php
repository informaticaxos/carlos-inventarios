<?php

require_once '../models/Producto.php';

class ProductoRepository {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $producto = new Producto($this->conn);
        return $producto->read();
    }

    public function getById($id) {
        $producto = new Producto($this->conn);
        $producto->id_producto = $id;
        $producto->readOne();
        return $producto;
    }

    public function create($data) {
        $producto = new Producto($this->conn);
        $producto->nombre_producto = $data['nombre_producto'];
        $producto->unidad_medida_producto = $data['unidad_medida_producto'];
        $producto->categoria_producto = $data['categoria_producto'];
        return $producto->create();
    }

    public function update($id, $data) {
        $producto = new Producto($this->conn);
        $producto->id_producto = $id;
        $producto->nombre_producto = $data['nombre_producto'];
        $producto->unidad_medida_producto = $data['unidad_medida_producto'];
        $producto->categoria_producto = $data['categoria_producto'];
        return $producto->update();
    }

    public function delete($id) {
        $producto = new Producto($this->conn);
        $producto->id_producto = $id;
        return $producto->delete();
    }

    public function getReport() {
        $producto = new Producto($this->conn);
        return $producto->read();
    }
}
?>
