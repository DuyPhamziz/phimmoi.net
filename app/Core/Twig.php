<?php

namespace App\Core;
use Twig\Extension\DebugExtension;

class Twig
{
    protected static $twig;

    public static function view($template, $data = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
$twig->addExtension(new DebugExtension());
        echo $twig->render($template, $data);
    }

    // ✅ Hàm toàn cục để add biến global
    public static function addGlobal($key, $value)
    {
        self::getEnvironment()->addGlobal($key, $value);
    }

    public static function getEnvironment()
    {
        if (self::$twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
            self::$twig = new \Twig\Environment($loader, [
                'debug' => true,
                'cache' => false,
            ]);
        }

        return self::$twig;
    }
}
