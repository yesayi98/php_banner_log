<?php

use HTTP\Controllers\IndexController;

return [
    '/banner.php:GET' => [
        'controller' => IndexController::class,
        'action' => 'index',
    ]
];