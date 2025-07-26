<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Core\Helpers;

class CommentController extends BaseController
{
    protected $commentModel;

    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new Comment();
    }

    public function send($movie_id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
            if (!isset($_SESSION['user'])) {
                echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
                return;
            }

            $user_id = $_SESSION['user']['userID'];
            $username = $_SESSION['user']['userFullName'] ?? 'Ẩn danh'; 
            $content = trim($_POST['comment']);
            $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

            $this->commentModel->sendAMessage($user_id, $movie_id, $content);

            echo json_encode([
                'success' => true,
                'username' => $username,
                'content' => htmlspecialchars($content),
                'parent_id' => $parent_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thiếu nội dung']);
        }
    }
    
}
