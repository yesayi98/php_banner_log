<?php

use Base\Response;

function basePath(): string
{
    return __DIR__;
}

function getConfigs($key = '')
{
    $configs = include ('configs.php') ?? [];
    if (!empty($key)) {
        return $configs[$key];
    }

    return $configs;
}

function assets($path = ''): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . getConfigs('assets_dir') . DIRECTORY_SEPARATOR . $path;
}

function response($content, $status = 200)
{
    return new Response($content, $status);
}