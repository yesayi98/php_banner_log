<?php

namespace Base;

class Request
{
    protected array $query;
    protected array $post;
    protected array $params;
    protected array $files;
    protected array $cookies;
    protected array $serverInfo;
    /**
     * @var array|false
     */
    protected $headers;

    protected function __construct(array $get, array $post, array $request, array $files, array $cookies, array $server)
    {
        $this->query = $get;
        $this->post = $post;
        $this->params = $request;
        $this->files = $files;

        $this->headers = getallheaders();

        $this->cookies = $cookies;
        $this->serverInfo = $server;
    }

    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_REQUEST, $_FILES, $_COOKIE, $_SERVER);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getPost(string $key = null)
    {
        if ($key) {
            return $this->post[$key];
        }

        return $this->post;
    }

    public function getQuery(string $key = null)
    {
        if ($key) {
            return $this->query[$key];
        }

        return $this->query;
    }

    /**
     * @param string|null $key
     * @return array
     */
    public function getFiles(string $key = null): array
    {
        if ($key) {
            return $this->files[$key];
        }

        return $this->files;
    }

    /**
     * @return array
     */
    public function getServerInfo(): array
    {
        return $this->serverInfo;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getUri(): string
    {
        return $this->serverInfo['SCRIPT_NAME'];
    }

    public function getMethod(): string
    {
        return $this->serverInfo['REQUEST_METHOD'];
    }

    /**
     * @return array|false
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getHeader(string $key = null)
    {
        return $this->headers[$key];
    }

    public function getIp()
    {
        if (!empty($this->getServerInfo()['HTTP_CLIENT_IP'])) {
            $ip = $this->getServerInfo()['HTTP_CLIENT_IP'];
        } elseif (!empty($this->getServerInfo()['HTTP_X_FORWARDED_FOR'])) {
            $ip = $this->getServerInfo()['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $this->getServerInfo()['REMOTE_ADDR'];
        }

        return $ip;
    }
}