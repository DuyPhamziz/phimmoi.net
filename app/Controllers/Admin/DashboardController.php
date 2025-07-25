<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        $this->render('admin/dashboard', [
            'page_title' => 'Tổng quan',
            'user' => $_SESSION['user'] ?? null,
        ]);
    }
    
}
