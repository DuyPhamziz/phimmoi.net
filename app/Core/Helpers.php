<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Helpers
{
    public static function render($view, $data = [])
    {
        // Khởi tạo loader cho Twig
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);

        // Lấy session người dùng (nếu có)
        $data['session'] = $_SESSION ?? [];

        // Kết nối cơ sở dữ liệu
        $pdo = \App\Core\Database::connect();

        // Lấy danh sách thể loại để đổ vào menu
        $stmt = $pdo->prepare("SELECT name_tag AS name, slug_tag AS slug FROM tags ORDER BY name_tag ASC");
        $stmt->execute();
        $genres = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Truyền genres vào tất cả các trang
        $data['genres'] = $genres;

        // Hiển thị view
        echo $twig->render($view . '.twig', $data);
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
