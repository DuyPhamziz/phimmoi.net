<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Models\Movie;
use App\Models\Comment;
use App\Core\Helpers;
use App\Models\History;
use App\Core\Twig;
use App\Controllers\BaseController;
use App\Models\Country;
use App\Models\Category;

class MovieController extends BaseController
{
    protected $movieModel;
    protected $TagModel;
    protected $CountryModel;
    protected $CategoryModel;
    protected $commentModel;
    public function __construct()
    {
        $this->movieModel = new Movie();
        $this->commentModel = new Comment();
        $this->TagModel = new Tag();
        $this->CountryModel = new Country();
        $this->CategoryModel = new Category();
    }
    public function home()
    {

        $hotMovies = $this->movieModel->getMoviesByTag('phim-hot', 10);
        $popularMovies = $this->movieModel->getMoviesByTag('popular', 10);

        $singleMovies = $this->movieModel->getMoviesByTag('phim-le', 10);



        Helpers::render('home', array_merge([
            'hotMovies' => $hotMovies,
            'popularMovies' => $popularMovies,
            'singleMovies' => $singleMovies,
        ], $this->getSharedData()));
    }

    public function info($slug)
    {


        if (!$slug) {
            Helpers::render404('Không tìm thấy phim hoặc tập phim!');
            return;
        }

        $movie = $this->movieModel->getMoviesBySlug($slug);
        if (!$movie || !isset($movie['id'])) {
            Helpers::render404('Không tìm thấy phim hoặc tập phim!');
            return;
        }

        $movieID = $movie['id'];
        $category = $this->movieModel->getCategoryForMovie($movieID);
        $episodes = $this->movieModel->getEpisodesForMovie($movieID);
        $rcmMovies = $this->movieModel->getRecommendedMovie($movieID);

        if (empty($episodes)) {
            Helpers::render404('Phim chưa có tập nào!');
            return;
        }

        Helpers::render('info', [
            'movie' => $movie,
            'category' => $category,
            'episodes' => $episodes,
            'rcmMovies' => $rcmMovies,
        ]);
    }
    // Gợi ý autocomplete JSON
    public function autocomplete()
    {
        $keyword = $_GET['q'] ?? '';
        $results = [];

        if ($keyword) {
            $results = $this->movieModel->searchMoviesByKeyword($keyword, 10);
        }

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }


    // Trang kết quả tìm kiếm
    public function search()
    {
        $keyword = $_GET['q'] ?? '';
        $movies = [];

        if ($keyword) {
            $movies = $this->movieModel->searchByName($keyword);
        }

        echo (new \App\Core\Twig)->view('search.twig', [
            'keyword' => $keyword,
            'movies' => $movies
        ]);
    }
    public function searchSlug($slug)
    {
        // Chuyển slug về từ khóa gốc
        $keyword = str_replace('-', ' ', $slug);
        $movies = $this->movieModel->searchByName($keyword);

        Helpers::render('search', [
            'keyword' => $keyword,
            'movies' => $movies
        ]);
    }



    public function watch($slug, $ep)
    {


        $historyModel = new History();

        if (empty($slug) || empty($ep)) {
            Helpers::render404('Không tìm thấy phim hoặc tập phim!');
            return;
        }


        $movie = $this->movieModel->getMoviesBySlug($slug);
        $movieID = $movie['id'] ?? null;
        // var_dump($movieID);
        // exit;

        if (!$movie || !$movieID) {
            Helpers::render404('Không tìm thấy phim hoặc tập phim!');
            return;
        }
        $category = $this->movieModel->getCategoryForMovie($movieID);
        $episodesList = $this->movieModel->getEpisodesForMovie($movieID);
        $currentEp = $this->movieModel->getEpisodesBySlugAndNum($slug, $ep);

        $comments = $this->commentModel->renderMessage($movieID);
        $rcmMovies = $this->movieModel->getRecommendedMovie($movieID);
        // var_dump($rcmMovies);
        // exit;

        if (!$currentEp) {
            Helpers::render404('Không tìm thấy phim hoặc tập phim!');
            return;
        }

        $progress = 0;
        if (isset($_SESSION['user'])) {
            $progress = $historyModel->getProgress($_SESSION['user']['id'], $movieID, (int)$currentEp['id']);
        }
        // var_dump($_SESSION['user']['id']);
        // var_dump($movieID);
        // var_dump($currentEp['id']);
        // var_dump($progress);
        // die;

        Helpers::render('watch', [
            'movie_id' => $movieID,
            'slug' => $slug,
            'ep' => $ep,
            'movie' => $movie,
            'category' => $category,
            'epForMovie' => $episodesList,
            'currentEp' => $currentEp,
            'save_progress' => $progress,
            'comments' => $comments,
            'rcmMovies' => $rcmMovies,
        ]);
    }
    public function handleCommentPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::render404('Yêu cầu không hợp lệ!');
            return;
        }

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            Helpers::redirect('/login');
            return;
        }

        $userID = $_SESSION['user']['id'] ?? null;
        $movieID = $_POST['movie_id'] ?? null;

        $slug = $_POST['slug'] ?? '';
        $ep = $_POST['ep'] ?? '';
        $content = trim($_POST['content'] ?? '');
        $parentID = $_POST['parent_id'] ?? null;
        // var_dump($movieID, $content, $slug, $ep);
        // exit;
        if (!$movieID || !$content || !$slug || !$ep) {
            header("Location: /");
            return;
        }

        $success = $this->commentModel->addComment($movieID, $userID, $content, $parentID);

        if (!$success) {
            $_SESSION['error'] = 'Không thể gửi bình luận. Vui lòng thử lại!';
        }

        Helpers::redirect("/$slug/$ep");
    }


    public function filter($slug)
 {
        $categorySlug = $_GET['category'] ?? null;
        $countrySlug = $_GET['country'] ?? null;

    if ($categorySlug || $countrySlug) {
        $movies = $this->movieModel->filterMovies($categorySlug, $countrySlug);

        Helpers::render('filter', array_merge([
            'movies' => $movies,
            'page_title' => 'Kết quả lọc phim',
            'slug' => null,
        ], $this->getSharedData()));
        return;
    }

        // 1. Thử tìm theo tag
        $tag = $this->TagModel->findBySlug($slug);
        if ($tag) {
            $movies = $this->movieModel->getByTagId($tag['id']);
            Helpers::render('filter', array_merge([
                'movies' => $movies,
                'page_title' => ' ' . $tag['name_tag'],
                'slug' => $slug,
            ], $this->getSharedData()));
            return;
        }

        // 2. Thử tìm theo quốc gia
        $country = $this->CountryModel->findBySlug($slug);
        if ($country) {
            $movies = $this->movieModel->getByCountryId($country['id']);
            Helpers::render('filter', array_merge([
                'movies' => $movies,
                'page_title' => 'Quốc gia: ' . $country['name'],
                'slug' => $slug,
            ], $this->getSharedData()));
            return;
        }

        // 3. Thử tìm theo thể loại
        $category = $this->CategoryModel->findBySlug($slug);
        if ($category) {
            $movies = $this->movieModel->getByCategoryId($category['id']);
            Helpers::render('filter', array_merge([
                'movies' => $movies,
                'page_title' => 'Thể loại: ' . $category['name_cat'],
                'slug' => $slug,
            ], $this->getSharedData()));
            return;
        }

        // 4. Không tìm thấy gì
        Helpers::render404('Không tìm thấy nội dung phù hợp!');
    }
    protected function getSharedData()
    {
        $countryModel = new \App\Models\Country();
        $categoryModel = new \App\Models\Category();

        return [
            'countries' => $countryModel->getAll(),
            'categories' => $categoryModel->getAll()
        ];
        
    }
}
