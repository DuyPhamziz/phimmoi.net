<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\User;
use App\core\Helpers;
use App\Models\Movie;
class SettingController extends BaseAdminController
{
    protected $userModel;
   
    public function __construct()
    {
        $this->userModel = new User();
        
    }
    public function index()
    {
        $users = $this->userModel->getAll();
        Helpers::render('admin/settings/index', [
            'users' => $users,
        ]);
    }
    public function setAdmin($id)
    {
        $userModel = new User(); // hoặc gọi thông qua DI nếu có
        $user = $userModel->find($id);

        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại.';
            header('Location: /admin/settings');
            exit;
        }

       
        $userModel->setAdmin($id);

        $_SESSION['success'] = 'Cấp quyền admin thành công!';
        header('Location: /admin/settings');
        exit;
    }

    public function revokeAdmin($id)
    {
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại.';
            header('Location: /admin/settings');
            exit;
        }

        $user['role'] = 'user'; // hoặc 'member', tuỳ hệ thống
        $userModel->revokeAdmin($id);

        $_SESSION['success'] = 'Đã thu hồi quyền admin!';
        header('Location: /admin/settings');
        exit;
    }
}
