<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
class Country {
    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function getAllCountries()
    {
        $stmt = $this->db->prepare("SELECT c.id, c.name, c.slug,
                                        COUNT(mc.movie_id) AS movie_count
                                    FROM countries c
                                    LEFT JOIN movie_countries mc ON c.id = mc.country_id
                                    GROUP BY c.id, c.name, c.slug
                                    ORDER BY movie_count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addToMovie($movieId, $countryId)
    {
        $stmt = $this->db->prepare("INSERT INTO movie_countries(movie_id, country_id)VALUES (?, ?);");
        $stmt->execute([$movieId, $countryId]);
    }
    public function updateMovieCountries($movie_id, $country_ids)
    {
        $this->db->prepare("DELETE FROM movie_countries WHERE movie_id = ?")->execute([$movie_id]);
        foreach ($country_ids as $cid) {
            $this->db->prepare("INSERT INTO movie_countries (movie_id, country_id) VALUES (?, ?)")->execute([$movie_id, $cid]);
        }
    }
    public function isSlugExists($slug)
    {
        $sql = "SELECT COUNT(*) FROM public.countries WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }

    public function createCountry($name, $slug)
    {
        if ($this->isSlugExists($slug)) {
            throw new Exception("Slug đã tồn tại");
        }
        $sql = "INSERT INTO public.countries(name, slug)
            VALUES (:name, :slug)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    public function updateCountry($id, $name, $slug)
    {
        $sql = "UPDATE public.countries
            SET name = :name, slug = :slug
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    public function deleteCountry($id)
    {
        $sql = "DELETE FROM public.countries
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
   public function getAll()
{
    $stmt = $this->db->prepare("SELECT * FROM countries");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    
   public function findBySlug($slug)
{
    $sql = "SELECT * FROM countries WHERE slug = :slug LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['slug' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}