<?php

require('helpers.php');

spl_autoload_register(function ($className) {
    $file = __DIR__ . '\\src\\' . $className . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

