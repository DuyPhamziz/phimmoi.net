<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Category
{
    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllCategories()
    {
        $stmt = $this->db->prepare("SELECT c.id, name_cat, c.slug_cat AS slug_cat, COUNT(mc.movie_id) AS movie_count
                                FROM categories c
                                LEFT JOIN movie_categories mc ON c.id = mc.category_id
                                GROUP BY c.id, c.name_cat, c.slug_cat
                                ORDER BY movie_count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addToMovie($movieId, $categoryId)
    {
        $stmt = $this->db->prepare("INSERT INTO movie_categories(movie_id, category_id) VALUES (?, ?)");
        $stmt->execute([$movieId, $categoryId]);
    }
    public function updateMovieCategories($movie_id, $category_ids)
    {
        $this->db->prepare("DELETE FROM movie_categories WHERE movie_id = ?")->execute([$movie_id]);
        foreach ($category_ids as $cid) {
            $this->db->prepare("INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)")->execute([$movie_id, $cid]);
        }
    }
    //admin
    public function isSlugExists($slug_cat) {
        $sql = "SELECT COUNT(*) FROM public.categories WHERE slug_cat = :slug_cat";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug_cat' => $slug_cat]);
        return $stmt->fetchColumn() > 0;
    }
    public function createCategory($name_cat, $slug_cat) {
        if ($this->isSlugExists($slug_cat)) {
            throw new Exception("Slug đã tồn tại");
        }
        $sql = "INSERT INTO public.categories(
                name_cat, slug_cat)
                VALUES (:name_cat, :slug_cat)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name_cat' => $name_cat,
            'slug_cat' => $slug_cat,
        ]);
    }
    public function updateCategory($id, $name_cat, $slug_cat) {
        if ($this->isSlugExists($slug_cat)) {
            throw new Exception("Slug đã tồn tại");
        }
        $sql = "UPDATE public.categories
                SET name_cat=?, slug_cat=?
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name_cat' => $name_cat,
            'slug_cat' => $slug_cat,
        ]);
    }
    public function deleteCategory($id) {
        $sql = "DELETE FROM public.categories
	            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    public function getAll()
{
    $stmt = $this->db->prepare("SELECT * FROM categories");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    public function findBySlug($slug_category)
{
    $sql = "SELECT * FROM categories WHERE slug_cat = :slug LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['slug' => $slug_category]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function countAll() {
    $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
    return $stmt->fetchColumn();
}

public function getPaginated($limit, $offset) {
    $limit = (int)$limit;
    $offset = (int)$offset;

    $sql = "SELECT c.*,
                   (SELECT COUNT(*) FROM movie_categories mc WHERE mc.category_id = c.id) AS movie_count
            FROM categories c
            LIMIT $limit OFFSET $offset";

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}




}
