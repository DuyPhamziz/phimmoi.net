<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Tag
{
    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function getAllTags()
    {
        $stmt = $this->db->prepare("SELECT t.id, t.name_tag, t.slug_tag,
                                        COUNT(mt.movie_id) AS movie_count
                                    FROM tags t
                                    LEFT JOIN movie_tags mt ON t.id = mt.tag_id
                                    GROUP BY t.id, t.name_tag, t.slug_tag
                                    ORDER BY movie_count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addToMovie($movieId, $tagId)
    {
        $stmt = $this->db->prepare("INSERT INTO movie_tags (movie_id, tag_id) VALUES (?, ?);");
        $stmt->execute([$movieId, $tagId]);
    }
    public function getTagsByMovieId($movieId)
    {
        $stmt = $this->db->prepare("SELECT tag_id FROM movie_tags WHERE movie_id = ?");
        $stmt->execute([$movieId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tag_id');
    }
    public function updateMovieTags($movie_id, $tagIds)
    {
        $this->db->prepare("DELETE FROM movie_tags WHERE movie_id = ?")->execute([$movie_id]);

        $stmt = $this->db->prepare("INSERT INTO movie_tags (movie_id, tag_id) VALUES (?, ?)");
        foreach ($tagIds as $tid) {
            $stmt->execute([$movie_id, $tid]);
        }
    }
    public function getTagBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM tags");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE slug_tag = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }
     //admin
    public function createTag($name_tag, $slug_tag) {
        $sql = "INSERT INTO public.tags(
                name_tag, slug_tag)
                VALUES (:name_tag, :slug_tag)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name_tag' => $name_tag,
            'slug_tag' => $slug_tag,
        ]);
    }
    public function updateTag($id, $name_tag, $slug_tag)
    {
        $sql = "UPDATE public.tags
                SET name_tag= :name_tag, slug_tag= :slug_tag
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name_tag' => $name_tag,
            'slug_tag' => $slug_tag,
            'id' => $id,
        ]);
    }
    public function deleteTag($id) {
        $sql = "DELETE FROM public.tags
	            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
        ]);
    }
}