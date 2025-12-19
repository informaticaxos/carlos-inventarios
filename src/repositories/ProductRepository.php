<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ProductModel.php';

/**
 * Repositorio para la entidad Product
 * Encapsula las operaciones CRUD contra la base de datos
 */
class ProductRepository
{
    private $pdo;

    /**
     * Constructor: obtiene la conexión a la base de datos
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo ?: Database::getConnection();
    }

    /**
     * Obtiene todos los registros de products
     *
     * @return array
     */
    public function findAll($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM producto ORDER BY id_producto DESC";
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de productos
     *
     * @return int
     */
    public function count()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM producto");
        return $stmt->fetchColumn();
    }

    /**
     * Obtiene un product por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un product (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param Product $product
     */
    public function save(Product $product)
    {
        if ($product->getIdProducto()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE producto SET nombre_producto = ?, unidad_medida_producto = ?, categoria_producto = ? WHERE id_producto = ?");
            $stmt->execute([
                $product->getNombreProducto(),
                $product->getUnidadMedidaProducto(),
                $product->getCategoriaProducto(),
                $product->getIdProducto()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO producto (nombre_producto, unidad_medida_producto, categoria_producto) VALUES (?, ?, ?)");
            $stmt->execute([
                $product->getNombreProducto(),
                $product->getUnidadMedidaProducto(),
                $product->getCategoriaProducto()
            ]);
            $product->setIdProducto($this->pdo->lastInsertId());
        }
    }

    /**
     * Elimina un product por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->execute([$id]);
    }
}
