<?php

// Definición de rutas REST para Cierre de Inventario
// Se fusionan con las rutas existentes

$routes = array_merge($routes ?? [], [
    // Obtener todos: https://nestorcornejo.com/carlos-inventarios/api/cierre-inventario
    'GET /cierre-inventario' => ['InventoryClosingController', 'getAll'],

    // Obtener uno por ID: https://nestorcornejo.com/carlos-inventarios/api/cierre-inventario/{id}
    'GET /cierre-inventario/{id}' => ['InventoryClosingController', 'getById'],

    // Crear: https://nestorcornejo.com/carlos-inventarios/api/cierre-inventario
    'POST /cierre-inventario' => ['InventoryClosingController', 'create'],

    // Actualizar: https://nestorcornejo.com/carlos-inventarios/api/cierre-inventario/{id}
    'PUT /cierre-inventario/{id}' => ['InventoryClosingController', 'update'],

    // Eliminar: https://nestorcornejo.com/carlos-inventarios/api/cierre-inventario/{id}
    'DELETE /cierre-inventario/{id}' => ['InventoryClosingController', 'delete'],
]);