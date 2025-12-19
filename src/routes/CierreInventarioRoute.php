<?php

$routes = [
    'GET /cierre_inventario' => ['CierreInventarioController', 'getAll'],
    'GET /cierre_inventario/rango' => ['CierreInventarioController', 'getByDateRange'],
    'GET /cierre_inventario/{id}' => ['CierreInventarioController', 'getById'],
    'POST /cierre_inventario' => ['CierreInventarioController', 'create'],
    'PUT /cierre_inventario/{id}' => ['CierreInventarioController', 'update'],
    'DELETE /cierre_inventario/{id}' => ['CierreInventarioController', 'delete'],
];