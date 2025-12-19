<?php

class Product
{
    private $id_producto;
    private $nombre_producto;
    private $unidad_medida_producto;
    private $categoria_producto;

    public function __construct($id_producto = null, $nombre_producto = null, $unidad_medida_producto = null, $categoria_producto = null)
    {
        $this->id_producto = $id_producto;
        $this->nombre_producto = $nombre_producto;
        $this->unidad_medida_producto = $unidad_medida_producto;
        $this->categoria_producto = $categoria_producto;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    public function getNombreProducto()
    {
        return $this->nombre_producto;
    }

    public function setNombreProducto($nombre_producto)
    {
        $this->nombre_producto = $nombre_producto;
    }

    public function getUnidadMedidaProducto()
    {
        return $this->unidad_medida_producto;
    }

    public function setUnidadMedidaProducto($unidad_medida_producto)
    {
        $this->unidad_medida_producto = $unidad_medida_producto;
    }

    public function getCategoriaProducto()
    {
        return $this->categoria_producto;
    }

    public function setCategoriaProducto($categoria_producto)
    {
        $this->categoria_producto = $categoria_producto;
    }
}