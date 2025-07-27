<?php

use App\Controllers\WoyofalController;

return [
    '/' => [
        'controller' => WoyofalController::class,
        'method' => 'index',
        'methods' => ['GET'],
    ],
    '/achat' => [
        'controller' => WoyofalController::class,
        'method' => 'achat',
        'methods' => ['POST'],
    ],
];
