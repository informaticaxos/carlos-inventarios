<?php

$routes = [
    'GET /producto' => ['ProductController', 'getAll'],
    'GET /producto/{id}' => ['ProductController', 'getById'],
    'POST /producto' => ['ProductController', 'create'],
    'PUT /producto/{id}' => ['ProductController', 'update'],
    'DELETE /producto/{id}' => ['ProductController', 'delete'],
];