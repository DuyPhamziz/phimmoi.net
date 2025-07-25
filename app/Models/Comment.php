<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Comment {
    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function sendAMessage($user_id, $movie_id, $content, $parent_id = null)
    {
        $sql = "INSERT INTO public.comments (
                user_id, movie_id, content, parent_id)
            VALUES (:user_id, :movie_id, :content, :parent_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $user_id,
            'movie_id' => $movie_id,
            'content' => htmlspecialchars($content),
            'parent_id' => $parent_id,
        ]);
    }

    public function renderMessage($movie_id)
    {
        $sql = "SELECT c.id, c.movie_id, m.slug_movie, c.user_id, u.name, u.avatar, u.icon, c.content, c.created_at, c.parent_id
                FROM public.comments c
                JOIN users u ON c.user_id = u.id
                JOIN movies m On m.id = c.movie_id
                WHERE c.movie_id = :movie_id
                ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'movie_id' => $movie_id,
        ]);
        $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sắp xếp thành cây bình luận
        $tree = [];
        $refs = [];

        foreach ($allComments as &$comment) {
            $comment['replies'] = []; // Khởi tạo replies rỗng
            $refs[$comment['id']] = &$comment;

            if ($comment['parent_id'] === null) {
                $tree[] = &$comment;
            } else {
                // Gắn comment vào replies của parent
                if (isset($refs[$comment['parent_id']])) {
                    $refs[$comment['parent_id']]['replies'][] = &$comment;
                }
            }
        }

        return $tree; 
    }

    public function addComment($movieID, $userID, $content, $parentID = null)
    {
        $sql = "INSERT INTO comments (movie_id, user_id, content, parent_id, created_at) 
            VALUES (:movie_id, :user_id, :content, :parent_id, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'movie_id' => $movieID,
            'user_id' => $userID,
            'content' => htmlspecialchars($content),
            'parent_id' => $parentID,
        ]);
    }
}