<?php 

namespace App\Controllers;

use App\Auth\GoogleAuth;
use App\Models\User;
use App\Core\Database;
use App\Core\Helpers;

class AuthController {
    protected $userModel;
    protected $google;

    public function __construct()
    {
        $this->google = new GoogleAuth();
        $this->userModel = new User(Database::connect());
    }
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        
        if (isset($_SESSION['user'])) {
            Helpers::redirect('/admin');
            return;
        }
        $authUrl = $this->google->getAuthUrl();
        
        Helpers::render('login',[
            'authUrl' => $authUrl
        ]);
    }
    public function callback() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if(!isset($_GET['code'])) {
            Helpers::render404('Không có mã xác thực từ Google.');
        }

        $token = $this->google->getAccessToken($_GET['code']);
        $googleUser = $this->google->getUserInfo($token);

        
        $user = $this->userModel->findByGoogleId($googleUser->getId());

        if (!$user) {
            $userInfo = $this->google->extractUserInfo($token);
            
            $user = $this->userModel->create($userInfo);
        }



        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            Helpers::redirect('/admin');
        } else {
            Helpers::redirect('/');
        }
        exit;
    }
    public function logout() {
        session_start();
        unset($_SESSION['user']);
        session_destroy();

        Helpers::redirect('/login');
    }
   
}