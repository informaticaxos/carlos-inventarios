<?php

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto ORDER BY id_producto DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE id_producto = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO producto (nombre_producto, unidad_medida_producto, categoria_producto) 
                VALUES (:nombre, :unidad, :categoria)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $data['nombre_producto'],
            'unidad' => $data['unidad_medida_producto'],
            'categoria' => $data['categoria_producto']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE producto SET 
                nombre_producto = :nombre, 
                unidad_medida_producto = :unidad, 
                categoria_producto = :categoria 
                WHERE id_producto = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre_producto'],
            'unidad' => $data['unidad_medida_producto'],
            'categoria' => $data['categoria_producto']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM producto WHERE id_producto = :id");
        return $stmt->execute(['id' => $id]);
    }
}