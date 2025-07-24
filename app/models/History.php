<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class History
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Lấy thời gian xem (giây) hiện tại
    public function getProgress($userId, $movieId, $episodeId): int
    {
        $stmt = $this->db->prepare("SELECT progress_seconds FROM history WHERE user_id = ? AND movie_id = ? AND episode_id = ?");
        $stmt->execute([$userId, $movieId, $episodeId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getHistory($userId): array
    {
        $stmt = $this->db->prepare("
                            SELECT 
                        h.movie_id,
                        h.progress_seconds,
                        h.watched_at,
                        m.*
                    FROM history h
                    JOIN movies m ON h.movie_id = m.id
                    WHERE h.user_id = ?
    ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function saveOrUpdateProgress($userId, $movieId, $episodeId, $progress)
    {


        // Kiểm tra dòng đã tồn tại chưa
        $stmt = $this->db->prepare("SELECT id FROM history WHERE user_id = ? AND episode_id = ?");
        $stmt->execute([$userId, $episodeId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $this->db->prepare("UPDATE history SET progress_seconds = ?, watched_at = CURRENT_TIMESTAMP WHERE user_id = ? AND episode_id = ?");
            return $stmt->execute([$progress, $userId, $episodeId]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO history (user_id, movie_id, episode_id, progress_seconds) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $movieId, $episodeId, $progress]);
        }
    }
}
