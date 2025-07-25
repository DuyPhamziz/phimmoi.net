<?php

namespace App\Models;


use App\Core\Database;


use PDO;

class Movie
{
    protected $conn;

    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function getAll()
    {

        $sqlGetAll = "SELECT 
                        m.*, 
                        STRING_AGG(DISTINCT c.name_cat, ', ') AS categories,
                        STRING_AGG(DISTINCT t.name_tag, ', ') AS tags,
                        STRING_AGG(DISTINCT ct.name, ', ') AS countries
                    FROM movies m
                    LEFT JOIN movie_categories mc ON mc.movie_id = m.id
                    LEFT JOIN categories c ON mc.category_id = c.id
                    LEFT JOIN movie_tags mt ON mt.movie_id = m.id
                    LEFT JOIN tags t ON mt.tag_id = t.id
                    LEFT JOIN movie_countries mct ON mct.movie_id = m.id
                    LEFT JOIN countries ct ON mct.country_id = ct.id
                    GROUP BY m.id";
        $stmt = $this->db->prepare($sqlGetAll);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function slugToKeywords($slug)
    {
        $slug = strtolower($slug);
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug); // bỏ dư space
        return explode(' ', trim($slug));
    }
    private function removeAccents($str)
    {
        $accents = [
            'à' => 'a',
            'á' => 'a',
            'ạ' => 'a',
            'ả' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'ầ' => 'a',
            'ấ' => 'a',
            'ậ' => 'a',
            'ẩ' => 'a',
            'ẫ' => 'a',
            'ă' => 'a',
            'ằ' => 'a',
            'ắ' => 'a',
            'ặ' => 'a',
            'ẳ' => 'a',
            'ẵ' => 'a',
            'è' => 'e',
            'é' => 'e',
            'ẹ' => 'e',
            'ẻ' => 'e',
            'ẽ' => 'e',
            'ê' => 'e',
            'ề' => 'e',
            'ế' => 'e',
            'ệ' => 'e',
            'ể' => 'e',
            'ễ' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'ị' => 'i',
            'ỉ' => 'i',
            'ĩ' => 'i',
            'ò' => 'o',
            'ó' => 'o',
            'ọ' => 'o',
            'ỏ' => 'o',
            'õ' => 'o',
            'ô' => 'o',
            'ồ' => 'o',
            'ố' => 'o',
            'ộ' => 'o',
            'ổ' => 'o',
            'ỗ' => 'o',
            'ơ' => 'o',
            'ờ' => 'o',
            'ớ' => 'o',
            'ợ' => 'o',
            'ở' => 'o',
            'ỡ' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'ụ' => 'u',
            'ủ' => 'u',
            'ũ' => 'u',
            'ư' => 'u',
            'ừ' => 'u',
            'ứ' => 'u',
            'ự' => 'u',
            'ử' => 'u',
            'ữ' => 'u',
            'ỳ' => 'y',
            'ý' => 'y',
            'ỵ' => 'y',
            'ỷ' => 'y',
            'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A',
            'Á' => 'A',
            'Ạ' => 'A',
            'Ả' => 'A',
            'Ã' => 'A',
            'Â' => 'A',
            'Ầ' => 'A',
            'Ấ' => 'A',
            'Ậ' => 'A',
            'Ẩ' => 'A',
            'Ẫ' => 'A',
            'Ă' => 'A',
            'Ằ' => 'A',
            'Ắ' => 'A',
            'Ặ' => 'A',
            'Ẳ' => 'A',
            'Ẵ' => 'A',
            'È' => 'E',
            'É' => 'E',
            'Ẹ' => 'E',
            'Ẻ' => 'E',
            'Ẽ' => 'E',
            'Ê' => 'E',
            'Ề' => 'E',
            'Ế' => 'E',
            'Ệ' => 'E',
            'Ể' => 'E',
            'Ễ' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Ị' => 'I',
            'Ỉ' => 'I',
            'Ĩ' => 'I',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ọ' => 'O',
            'Ỏ' => 'O',
            'Õ' => 'O',
            'Ô' => 'O',
            'Ồ' => 'O',
            'Ố' => 'O',
            'Ộ' => 'O',
            'Ổ' => 'O',
            'Ỗ' => 'O',
            'Ơ' => 'O',
            'Ờ' => 'O',
            'Ớ' => 'O',
            'Ợ' => 'O',
            'Ở' => 'O',
            'Ỡ' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Ụ' => 'U',
            'Ủ' => 'U',
            'Ũ' => 'U',
            'Ư' => 'U',
            'Ừ' => 'U',
            'Ứ' => 'U',
            'Ự' => 'U',
            'Ử' => 'U',
            'Ữ' => 'U',
            'Ỳ' => 'Y',
            'Ý' => 'Y',
            'Ỵ' => 'Y',
            'Ỷ' => 'Y',
            'Ỹ' => 'Y',
            'Đ' => 'D',
        ];
        return strtr($str, $accents);
    }


    public function searchMoviesByName($keyword)
    {
        $stmt = $this->db->prepare("
        SELECT * FROM movies 
        WHERE unaccent(lower(name)) LIKE unaccent(lower(:kw)) 
        LIMIT 20
    ");
        $stmt->execute(['kw' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    }


    public function getMoviesBySlug($slug)
    {


        $sqlGetMovieBySlug = "SELECT * 
                                FROM movies m
                                -- JOIN movie_categories mc ON m.id = mc.movie_id
                                -- JOIN categories c ON mc.category_id = c.id
                                WHERE m.slug_movie = :slug 
                                LIMIT 1";
        $stmt = $this->db->prepare($sqlGetMovieBySlug);
        $stmt->execute([
            'slug' => $slug
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getCategoryForMovie($movieID)
    {

        $sqlGetCategoryForMovie = "SELECT m.id AS movie_id, c.id AS category_id, c.name_cat, c.slug_cat, m.slug_movie
                                    FROM movie_categories mc
                                    JOIN movies m ON mc.movie_id = m.id
                                    JOIN categories c ON mc.category_id = c.id
                                    WHERE m.id = :movieID
                                    ";
        $stmt = $this->db->prepare($sqlGetCategoryForMovie);
        $stmt->execute([
            'movieID' => $movieID
        ]);
        return $stmt->fetchAll();
    }
    public function getEpisodesForMovie($movieID)
    {

        $sqlGetEpisodesForMovie = "SELECT e.id, e.episode_number, e.link_m3u8
                                    FROM episodes e
                                    WHERE movie_id = :movieID";
        $stmt = $this->db->prepare($sqlGetEpisodesForMovie);
        $stmt->execute([
            'movieID' => $movieID
        ]);
        return $stmt->fetchAll((PDO::FETCH_ASSOC));
    }
    public function getEpisodesBySlugAndNum($slug, $episodes_number)
    {

        $sqlGetEpisodesBySlugAndNum = "SELECT e.*
                                        FROM episodes e
                                        JOIN movies m ON e.movie_id = m.id
                                        WHERE m.slug_movie = :slug AND e.episode_number = :episodes_number";
        $stmt = $this->db->prepare($sqlGetEpisodesBySlugAndNum);
        $stmt->execute([
            'slug' => $slug,
            'episodes_number' => $episodes_number
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getRecommendedMovie($movieID)
    {
        $sql = "WITH original_categories AS (
                    SELECT category_id
                    FROM movie_categories
                    WHERE movie_id = :movie_id_1
                ),
                matched_movies AS (
                    SELECT mc.movie_id
                    FROM movie_categories mc
                    JOIN original_categories oc ON mc.category_id = oc.category_id
                    WHERE mc.movie_id != :movie_id_2
                    GROUP BY mc.movie_id
                    HAVING COUNT(DISTINCT mc.category_id) = (SELECT COUNT(*) FROM original_categories)
                )
                SELECT m.*
                FROM matched_movies mm
                JOIN movies m ON m.id = mm.movie_id
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'movie_id_1' => $movieID,
            'movie_id_2' => $movieID,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getMoviesByGenreSlug($slug, $limit = 12, $offset = 0)
    {
        $sql = "SELECT m.* FROM movies m
            JOIN movie_categories mc ON m.id = mc.movie_id
            JOIN categories c ON mc.category_id = c.id
            WHERE c.slug_cat = :slug
            ORDER BY m.updated_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSelectedCategories($movieId)
    {
        $sql = "SELECT category_id FROM movie_categories WHERE movie_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$movieId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
    }
    public function getSelectedCountries($movieId)
    {
        $sql = "SELECT country_id FROM movie_countries WHERE movie_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$movieId]);
        return array_column($stmt->fetchAll(), 'country_id');
    }

    // Thêm, xóa, phía Admin

    public function insert($data)
    {
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        $sql = "INSERT INTO movies(name_eng, name_vn, slug_movie, duration, director, actors, description, poster_url, year, type, quality, lang, thum_url, banner_url, status_id)
                VALUES (:name_eng, :name_vn, :slug_movie, :duration, :director, :actors, :description, :poster_url, :year, :type, :quality, :lang,  :thum_url, :banner_url, :status_id)";
        $stmt = $this->db->prepare("$sql");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function update($id, $data)
    {
        $sql = "UPDATE movies SET 
        name_eng = :name_eng,
        name_vn = :name_vn,
        slug_movie = :slug_movie,
        duration = :duration,
        director = :director,
        actors = :actors,
        description = :description,
        poster_url = :poster_url,
        year = :year,
        type = :type,
        quality = :quality,
        lang = :lang,
        thum_url = :thum_url,
        banner_url = :banner_url,
        status_id = :status_id
        WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function removeAllCategories($movieId)
    {
        $stmt = $this->db->prepare("DELETE FROM movie_categories WHERE movie_id = ?");
        $stmt->execute([$movieId]);
    }

    public function removeAllCountries($movieId)
    {
        $stmt = $this->db->prepare("DELETE FROM movie_countries WHERE movie_id = ?");
        $stmt->execute([$movieId]);
    }
    public function getMoviesByCategorySlug($slug, $limit, $offset)
    {
        $sql = "SELECT m.*
            FROM movies m
            JOIN movie_categories mc ON m.id = mc.movie_id
            JOIN categories c ON mc.category_id = c.id
            WHERE c.slug_cat = :slug
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getStatus()
    {
        $sql = "SELECT *
	            FROM status;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function searchMoviesByKeyword($keyword, $limit = null)
    {
        $sql = "SELECT id, name_vn, slug_movie, thum_url FROM movies WHERE LOWER(name_vn) LIKE LOWER(:keyword)";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchByName($keyword)
    {
        $keyword = trim($keyword);

        // Nếu chỉ có 1 từ → tìm gần đúng trong name_unsigned
        if (str_word_count(str_replace('-', ' ', $keyword)) === 1) {
            $keyword = $this->removeAccents(mb_strtolower($keyword));

            $stmt = $this->db->prepare("
            SELECT * FROM movies 
            WHERE slug_movie LIKE :kw 
            LIMIT 20
        ");
            $stmt->execute(['kw' => '%' . $keyword . '%']);
            return $stmt->fetchAll();
        }

        // Nếu có nhiều từ → tìm theo slug chứa tất cả các từ
        $keywords = $this->slugToKeywords($keyword);

        $where = [];
        $params = [];
        foreach ($keywords as $i => $word) {
            $where[] = "slug_movie ILIKE :kw$i";
            $params["kw$i"] = '%' . $word . '%';
        }

        $sql = "SELECT * FROM movies WHERE " . implode(" AND ", $where) . " LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    public function getMoviesByCountrySlug($slug, $limit = 12, $offset = 0)
    {
        $sql = "SELECT m.* FROM movies m
            JOIN movie_countries mc ON m.id = mc.movie_id
            JOIN countries c ON mc.country_id = c.id
            WHERE c.slug = :slug
            ORDER BY m.updated_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByIdWithTagsAndCategories($id)
    {
        $sql = "SELECT * FROM movies WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetch();

        if (!$movie) {
            return null;
        }

        // Lấy thể loại
        $sqlCat = "SELECT c.* FROM categories c
               JOIN movie_categories mc ON c.id = mc.category_id
               WHERE mc.movie_id = :id";
        $stmt = $this->db->prepare($sqlCat);
        $stmt->execute(['id' => $id]);
        $categories = $stmt->fetchAll();

        // Lấy tag
        $sqlTag = "SELECT t.* FROM tags t
               JOIN movie_tags mt ON t.id = mt.tag_id
               WHERE mt.movie_id = :id";
        $stmt = $this->db->prepare($sqlTag);
        $stmt->execute(['id' => $id]);
        $tags = $stmt->fetchAll();
        // Lấy tag
        $sqlCt = "SELECT c.* FROM countries c
               JOIN movie_countries mc ON c.id = mc.country_id
               WHERE mc.movie_id = :id";
        $stmt = $this->db->prepare($sqlCt);
        $stmt->execute(['id' => $id]);
        $Countries = $stmt->fetchAll();

        $movie['categories'] = $categories;
        $movie['tagsforMovie'] = $tags;
        $movie['Countries'] = $Countries;
       
        return $movie;
    }


    public function countMoviesByCountry($slug)
    {
        $sql = "SELECT COUNT(*) FROM movies m
            JOIN movie_countries mc ON m.id = mc.movie_id
            JOIN countries c ON mc.country_id = c.id
            WHERE c.slug = :slug";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn();
    }

    public function getMoviesByTag($slug, $limit = 12, $offset = 0)
    {
        $sql = "SELECT m.*
            FROM movies m
            JOIN movie_tags mt ON m.id = mt.movie_id
            JOIN tags t ON t.id = mt.tag_id
            WHERE t.slug_tag = :slug
            ORDER BY m.updated_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function countMoviesByTag($slug)
    {
        $sql = "SELECT COUNT(*) FROM movies m
            JOIN movie_tags mt ON m.id = mt.movie_id
            JOIN tags t ON t.id = mt.tag_id
            WHERE t.slug_tag = :slug";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    public function getAllTags()
    {
        $stmt = $this->db->prepare("SELECT * FROM tags ORDER BY name_tag ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllCategories()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name_cat ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getByTagId($tag_id)
    {
        $stmt = $this->db->prepare("
        SELECT m.* FROM movies m
        JOIN movie_tags mt ON m.id = mt.movie_id
        WHERE mt.tag_id = :tag_id
    ");
        $stmt->execute(['tag_id' => $tag_id]);
        return $stmt->fetchAll();
    }
    public function getByCategoryId($category_id)
    {
        $stmt = $this->db->prepare("
        SELECT m.* FROM movies m
        JOIN movie_categories mc ON m.id = mc.movie_id
        WHERE mc.category_id = :category_id
    ");
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll();
    }
    public function getByCountryId($country_id)
    {
        $stmt = $this->db->prepare("
        SELECT m.* FROM movies m
        JOIN movie_countries mc ON m.id = mc.movie_id
        WHERE mc.country_id = :country_id
    ");
        $stmt->execute(['country_id' => $country_id]);
        return $stmt->fetchAll();
    }
    public function filterMovies($categorySlug = null, $countrySlug = null, $year = null)
    {
        $sql = "
        SELECT DISTINCT m.* FROM movies m
        LEFT JOIN movie_categories mc ON m.id = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.id
        LEFT JOIN movie_countries mcty ON m.id = mcty.movie_id
        LEFT JOIN countries ct ON mcty.country_id = ct.id
        WHERE 1=1
    ";

        $params = [];

        if ($categorySlug) {
            $sql .= " AND c.slug_cat = :cat_slug";
            $params['cat_slug'] = $categorySlug;
        }

        if ($countrySlug) {
            $sql .= " AND ct.slug = :cty_slug";
            $params['cty_slug'] = $countrySlug;
        }

        if ($year) {
            $sql .= " AND m.release_year = :year";
            $params['year'] = $year;
        }

        $sql .= " ORDER BY m.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function countMovie() {
        $sql = "SELECT COUNT(m.id) AS cnt
            FROM public.movies m";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countEpisode()
    {
        $sql = "SELECT COUNT(e.id) AS cnt
                FROM public.episodes e";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countCategory()
    {
        $sql = "SELECT COUNT(c.id) AS cnt
                FROM public.categories c";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countCountry()
    {
        $sql = "SELECT COUNT(c.id) AS cnt
                FROM public.countries c";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countComment()
    {
        $sql = "SELECT COUNT(c.id) AS cnt
                FROM public.comments c";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countUser()
    {
        $sql = "SELECT COUNT(u.id) AS cnt
                FROM public.users u";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countTag()
    {
        $sql = "SELECT COUNT(t.id) AS cnt
                FROM public.tags t";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM movies");
        return $stmt->fetchColumn();
    }

    public function getPaginated($limit, $offset)
    {
        $stmt = $this->db->prepare("
        SELECT 
            m.*, 
            STRING_AGG(DISTINCT c.name_cat, ', ') AS categories,
            STRING_AGG(DISTINCT t.name_tag, ', ') AS tags,
            STRING_AGG(DISTINCT ct.name, ', ') AS countries
        FROM movies m
        LEFT JOIN movie_categories mc ON mc.movie_id = m.id
        LEFT JOIN categories c ON mc.category_id = c.id
        LEFT JOIN movie_tags mt ON mt.movie_id = m.id
        LEFT JOIN movie_countries mct ON mct.movie_id = m.id
        LEFT JOIN countries ct ON mct.country_id = ct.id
        LEFT JOIN tags t ON mt.tag_id = t.id
        GROUP BY m.id
        ORDER BY id DESC
        LIMIT :lim OFFSET :off
    ");
        $stmt->bindValue(':lim', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
