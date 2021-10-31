<?php

namespace Base;

class App
{
    /**
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * @var array
     */
    private static array $container = [];

    private function __construct()
    {

    }

    /**
     * @return self
     */
    public static function singleton(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run()
    {
        return $this->get('router')->dispatch();
    }

    public function prepare(): App
    {
        self::register('router', new Router($this, include(basePath() . '\\routes.php')));
        self::register('request', Request::createFromGlobals());
        self::register('db_connection', new Connection(...array_values(getConfigs('db'))));

        return static::$instance;
    }

    public static function register(string $key, object $instance) {
        static::$container[$key] = $instance;
    }

    public static function get(string $key) {
        return static::$container[$key];
    }
}