<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Episode
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function createMany($movieId, $episodes)
    {
        $stmt = $this->db->prepare("INSERT INTO episodes(movie_id, episode_number, title, link_m3u8, created_at) VALUES (?, ?, ?, ?, ?)");

        $epNum = 1;
        foreach ($episodes as $ep) {
            $stmt->execute([
                $movieId,
                $epNum++,
                $ep['title'],
                $ep['url'],
                date('Y-m-d H:i:s')
            ]);
        }
    }

    public function getByMovie($movieId)
    {
        $stmt = $this->db->prepare("SELECT * FROM episodes WHERE movie_id = ?");
        $stmt->execute([$movieId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM episodes WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function replaceAll($movieId, $episodes)
    {
        // Xóa tập cũ
        $stmt = $this->db->prepare("DELETE FROM episodes WHERE movie_id = ?");
        $stmt->execute([$movieId]);

        // Thêm tập mới
        $stmt = $this->db->prepare("INSERT INTO episodes (movie_id, episode_number, title, link_m3u8, created_at) VALUES (?, ?, ?, ?, NOW())");

        foreach ($episodes as $index => $ep) {
            $stmt->execute([
                $movieId,
                $index + 1,
                $ep['title'],
                $ep['url']
            ]);
        }
    }
    public function getAllWithMovieName()
    {
        $sql = "SELECT ep.*, m.name_vn AS movie_name, s.name AS status,
                    (SELECT COUNT(*) FROM episodes WHERE movie_id = ep.movie_id) AS total_episode
                FROM episodes ep
                JOIN movies m ON ep.movie_id = m.id
                LEFT JOIN status s ON m.status_id = s.id
                ORDER BY ep.movie_id ASC, ep.episode_number ASC;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // admin

    public function createEpisode($data)
    {
        $sql = "INSERT INTO public.episodes(
                        movie_id, episode_number, title, link_m3u8)
                        VALUES (:movie_id, :episode_number, :title, :link_m3u8)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'movie_id' => $data['movie_id'],
            'episode_number' => $data['episode_number'],
            'title' => $data['title'],
            'link_m3u8' => $data['link_m3u8'],
        ]);
    }

    public function updateEpisode($id, $data)
    {
        $sql = "UPDATE episodes SET 
                movie_id = :movie_id, 
                episode_number = :episode_number, 
                title = :title, 
                link_m3u8 = :link_m3u8
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    public function deleteEpisode($id) {
        $sql = "DELETE FROM public.episodes
	            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    // Trả về các phim duy nhất có tập phim
public function getDistinctMoviesWithEpisodes()
{
    $stmt = $this->db->query("
        SELECT DISTINCT m.id AS movie_id, m.name_vn AS movie_name
        FROM movies m
        JOIN episodes e ON m.id = e.movie_id
        ORDER BY m.id DESC
    ");
    return $stmt->fetchAll();
}

// Trả về các tập theo danh sách ID phim
public function getEpisodesByMovieIds($movieIds)
{
    if (empty($movieIds)) return [];

    $placeholders = implode(',', array_fill(0, count($movieIds), '?'));
    $sql = "
        SELECT e.*, m.name_vn AS movie_name
        FROM episodes e
        JOIN movies m ON e.movie_id = m.id
        WHERE m.id IN ($placeholders)
        ORDER BY m.id DESC, e.episode_number ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($movieIds);

    return $stmt->fetchAll();
}

}
