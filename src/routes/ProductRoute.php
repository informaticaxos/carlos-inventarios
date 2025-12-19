<?php

/**
 * Definición de rutas para la API de Products
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Products
$routes = [
    // Obtener todos los productos: https://nestorcornejo.com/carlos-inventarios/api/producto
    'GET /producto' => ['ProductController', 'getAll'],

    // Obtener un producto por ID: https://nestorcornejo.com/carlos-inventarios/api/producto/{id}
    'GET /producto/{id}' => ['ProductController', 'getById'],

    // Crear un producto: https://nestorcornejo.com/carlos-inventarios/api/producto
    'POST /producto' => ['ProductController', 'create'],

    // Actualizar un producto: https://nestorcornejo.com/carlos-inventarios/api/producto/{id}
    'PUT /producto/{id}' => ['ProductController', 'update'],

    // Eliminar un producto: https://nestorcornejo.com/carlos-inventarios/api/producto/{id}
    'DELETE /producto/{id}' => ['ProductController', 'delete'],
];
