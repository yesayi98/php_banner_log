<?php

namespace Base;

use HttpResponse;

class Response
{
    /**
     * @var
     */
    private static $content;

    public function __construct($content, $status = 200)
    {
        static::status($status);

        if (is_array($content) || is_object($content)) {
            static::setContent(json_encode($content));
        }elseif (is_file($content)) {
            static::setFile($content);
        }else{
            static::setContent($content);
        }
    }

    /**
     * @param $content
     * @param int|mixed $status
     */
    public static function sendResponse($content, $status)
    {
        static::status($status);
        if (is_file($content)) {
            static::setFile($content);
            static::send();
        }

        if (is_array($content) || is_object($content)) {
            $content = json_encode($content);
        }

        static::setContent($content);
        static::send();
    }

    public static function status($status)
    {
        http_response_code($status);
    }

    public static function setFile($content)
    {
        if (file_exists($content)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($content));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($content));
            ob_clean();
            flush();
        }

        self::$content = readfile($content);
    }

    /**
     * @param mixed $content
     */
    public static function setContent($content): void
    {
        self::$content = $content;
    }

    public static function send()
    {
        echo self::$content;
    }
}