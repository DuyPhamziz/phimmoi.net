<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Helpers
{
    public static function render($view, $data = [])
{
    // Lấy shared data từ BaseController
    $base = new \App\Controllers\BaseController();
    $shared = $base->getSharedData();

    // Gộp tất cả
    $data = array_merge($data, $shared);

    // Session và genres giữ nguyên
    $data['session'] = $_SESSION ?? [];

    $pdo = \App\Core\Database::connect();
    $stmt = $pdo->prepare("SELECT name_tag AS name, slug_tag AS slug FROM tags ORDER BY name_tag ASC");
    $stmt->execute();
    $genres = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $data['genres'] = $genres;

    echo (new \Twig\Environment(
        new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views'),
        ['debug' => true, 'cache' => false]
    ))->render($view . '.twig', $data);
}


    public static function redirect($path = '/')
    {
        $base = $_ENV['BASE_URL'] ?? '';
        header('Location: ' . rtrim($base, '/') . '/' . ltrim($path, '/'));
        exit;
    }
    public static function render404($message = 'Không tìm thấy trang')
    {
        http_response_code(404);
        self::render('errors/404', ['message' => $message]);
    }

}
