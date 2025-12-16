<?php

class Producto {
    private $conn;
    private $table_name = "producto";

    public $id_producto;
    public $nombre_producto;
    public $unidad_medida_producto;
    public $categoria_producto;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id_producto, nombre_producto, unidad_medida_producto, categoria_producto FROM " . $this->table_name . " ORDER BY nombre_producto";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre_producto=:nombre_producto, unidad_medida_producto=:unidad_medida_producto, categoria_producto=:categoria_producto";

        $stmt = $this->conn->prepare($query);

        $this->nombre_producto = htmlspecialchars(strip_tags($this->nombre_producto));
        $this->unidad_medida_producto = htmlspecialchars(strip_tags($this->unidad_medida_producto));
        $this->categoria_producto = htmlspecialchars(strip_tags($this->categoria_producto));

        $stmt->bindParam(":nombre_producto", $this->nombre_producto);
        $stmt->bindParam(":unidad_medida_producto", $this->unidad_medida_producto);
        $stmt->bindParam(":categoria_producto", $this->categoria_producto);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readOne() {
        $query = "SELECT id_producto, nombre_producto, unidad_medida_producto, categoria_producto FROM " . $this->table_name . " WHERE id_producto = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_producto);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nombre_producto = $row['nombre_producto'];
        $this->unidad_medida_producto = $row['unidad_medida_producto'];
        $this->categoria_producto = $row['categoria_producto'];
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre_producto = :nombre_producto, unidad_medida_producto = :unidad_medida_producto, categoria_producto = :categoria_producto WHERE id_producto = :id_producto";

        $stmt = $this->conn->prepare($query);

        $this->nombre_producto = htmlspecialchars(strip_tags($this->nombre_producto));
        $this->unidad_medida_producto = htmlspecialchars(strip_tags($this->unidad_medida_producto));
        $this->categoria_producto = htmlspecialchars(strip_tags($this->categoria_producto));
        $this->id_producto = htmlspecialchars(strip_tags($this->id_producto));

        $stmt->bindParam(':nombre_producto', $this->nombre_producto);
        $stmt->bindParam(':unidad_medida_producto', $this->unidad_medida_producto);
        $stmt->bindParam(':categoria_producto', $this->categoria_producto);
        $stmt->bindParam(':id_producto', $this->id_producto);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_producto = ?";

        $stmt = $this->conn->prepare($query);
        $this->id_producto = htmlspecialchars(strip_tags($this->id_producto));
        $stmt->bindParam(1, $this->id_producto);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
