<?php

namespace App\controllers\Admin;

use App\Core\Helpers;

class BaseAdminController
{
    protected function getAdminMenu(): array
    {
        return [
            [
                'label' => 'Tổng quan',
                'icon' => 'home',
                'url' => '/admin'
            ],
            [
                'label' => 'Phim',
                'icon' => 'film',
                'url' => '/admin/movies'
            ],
            [
                'label' => 'Tập phim',
                'icon' => 'video',
                'url' => '/admin/episodes'
            ],
            [
                'label' => 'Thể loại',
                'icon' => 'icons',
                'url' => '/admin/categories'
            ],
            [
                'label' => 'Nhãn',
                'icon' => 'tags',
                'url' => '/admin/tags'
            ],
            [
                'label' => 'Quốc gia',
                'icon' => 'earth-asia',
                'url' => '/admin/countries'
            ],
            [
                'label' => 'Cài đặt',
                'icon' => 'gear',
                'url' => '/admin/settings'
            ],
        ];
    }
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: /');
            exit;
        }

    }
    protected function render(string $view, array $data = [])
    {
        // Biến dùng chung cho layout sidebar
        $layoutData = [
            'base_url ' => $_ENV['BASE_URL'],
            'current_url' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            'menu' => $this->getAdminMenu(),
            'user' => $_SESSION['user'] ?? null,
        ];

        // Gộp dữ liệu layout + dữ liệu riêng của trang
        $data = array_merge($layoutData, $data);

        Helpers::render($view, $data);
    }
}
