<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;

class MovieController extends BaseAdminController
{
    protected $movieModel;
    protected $CategoryModel;
    protected $CountryModel;
    protected $EpisodeModel;
    protected $TagModel;
    public function __construct()
    {
        $this->movieModel = new Movie();
        $this->CategoryModel = new Category();
        $this->CountryModel = new Country();
        $this->EpisodeModel = new Episode();
        $this->TagModel = new Tag();
    }
    public function index()
    {
        $movies = $this->movieModel->getAll();
        $this->render('admin/movies/index', [
            'movies' => $movies,
        ]);
    }
    public function show($id)
    {
        $movies = $this->movieModel->findByIdWithTagsAndCategories($id);
        $this->render('admin/movies/show', [
            'movie' => $movies,
        ]);
    }
    public function create()
    {
        $this->render('admin/movies/create', [
            'page_title' => 'Thêm phim',
            'status' => $this->movieModel->getStatus(),
            'categories' => $this->CategoryModel->getAllCategories(),
            'countries' => $this->CountryModel->getAllCountries(),
            'user' => $_SESSION['user'] ?? null,
        ]);
    }
    public function edit($id)
    {
        $movie = $this->movieModel->find($id);
        $categories = $this->CategoryModel->getAllCategories();
        $countries = $this->CountryModel->getAllCountries();
        $episodes = $this->EpisodeModel->getByMovie($id);
        $selected_countries = $this->movieModel->getSelectedCountries($id);
        $selected_categories = $this->movieModel->getSelectedCategories($id);

        $this->render('admin/movies/edit', [
            'movie' => $movie,
            'status' => $this->movieModel->getStatus(),
            'categories' => $categories,
            'countries' => $countries,
            'episodes' => $episodes,
            'page_title' => 'Chỉnh sửa phim',
            'selected_categories' => $selected_categories,
            'selected_countries' => $selected_countries,
        ]);
    }
    public function tag($id)
    {
        $movie = $this->movieModel->find($id);
        $tags = $this->TagModel->getAllTags();
        $currentTags = $this->TagModel->getTagsByMovieId($id);

        $this->render('admin/movies/tag', [
            'movie' => $movie,
            'tags' => $tags,
            'currentTags' => $currentTags,
            'page_title' => 'Gán nhãn cho phim'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $slug = !empty($data['slug_movie']) ? $data['slug_movie'] : 'movie-' . bin2hex(random_bytes(4));

            $posterPath = $this->uploadFile(
                $_FILES['poster_url'],
                'posters',
                $slug,
                $data['poster_old'] ?? null
            );

            $thumPath = $this->uploadFile(
                $_FILES['thum_url'],
                'thumnail',
                $slug,
                $data['thum_old'] ?? null
            );

            $bannerPath = $this->uploadFile(
                $_FILES['banner_url'],
                'banner',
                $slug,
                $data['banner_old'] ?? null
            );


            $movieData = [
                'name_eng'    => !empty($data['name_eng']) ? $data['name_eng'] : 'Unknown',
                'name_vn'     => !empty($data['name_vn']) ? $data['name_vn'] : 'Chưa rõ',
                'slug_movie'  => $slug,
                'duration'    => !empty($data['duration']) ? $data['duration'] : 'Chưa rõ',
                'director'    => !empty($data['director']) ? $data['director'] : 'Chưa rõ',
                'actors'      => !empty($data['actors']) ? $data['actors'] : 'Chưa rõ',
                'description' => !empty($data['description']) ? $data['description'] : 'Chưa rõ',
                'poster_url'  => $posterPath,
                'year'        => !empty($data['year']) ? (int)$data['year'] : null,
                'type'        => !empty($data['type']) ? $data['type'] : 'movie',
                'quality'     => !empty($data['quality']) ? $data['quality'] : 'Chưa rõ',
                'lang'        => !empty($data['lang']) ? $data['lang'] : 'Chưa rõ',
                'thum_url'    => $thumPath,
                'banner_url'  => $bannerPath,
                'status_id'   => !empty($data['status_id']) ? (int)$data['status_id'] : 1,
            ];
            $movieId = $this->movieModel->insert($movieData);

            foreach ($data['categories'] as $catId) {
                $this->CategoryModel->addToMovie($movieId, $catId);
            }
            foreach ($data['countries'] as $countryId) {
                $this->CountryModel->addToMovie($movieId, $countryId);
            }
            if (!empty($data['episodes'])) {
                $this->EpisodeModel->createMany($movieId, $data['episodes']);
            }
            header('Location: ' . $_ENV['BASE_URL'] . '/admin/movies');
            exit;
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $slug = !empty($data['slug_movie']) ? $data['slug_movie'] : 'movie-' . bin2hex(random_bytes(4));

            // Lấy phim cũ để biết đường dẫn ảnh cũ
            $movie = $this->movieModel->find($id);

            // Upload ảnh nếu có chọn ảnh mới, xóa ảnh cũ
            $posterPath = $this->uploadFile(
                $_FILES['poster_url'],
                'posters',
                $slug,
                $data['poster_old'] ?? null
            );

            $thumPath = $this->uploadFile(
                $_FILES['thum_url'],
                'thumnail',
                $slug,
                $data['thum_old'] ?? null
            );

            $bannerPath = $this->uploadFile(
                $_FILES['banner_url'],
                'banner',
                $slug,
                $data['banner_old'] ?? null
            );


            $movieData = [
                'name_eng'    => !empty($data['name_eng']) ? $data['name_eng'] : 'Unknown',
                'name_vn'     => !empty($data['name_vn']) ? $data['name_vn'] : 'Chưa rõ',
                'slug_movie'  => $slug,
                'duration'    => !empty($data['duration']) ? $data['duration'] : 'Chưa rõ',
                'director'    => !empty($data['director']) ? $data['director'] : 'Chưa rõ',
                'actors'      => !empty($data['actors']) ? $data['actors'] : 'Chưa rõ',
                'description' => !empty($data['description']) ? $data['description'] : 'Chưa rõ',
                'poster_url'  => $posterPath,
                'year'        => !empty($data['year']) ? (int)$data['year'] : null,
                'type'        => !empty($data['type']) ? $data['type'] : 'movie',
                'quality'     => !empty($data['quality']) ? $data['quality'] : 'Chưa rõ',
                'lang'        => !empty($data['lang']) ? $data['lang'] : 'Chưa rõ',
                'thum_url'    => $thumPath,
                'banner_url'  => $bannerPath,
                'status_id'   => !empty($data['status_id']) ? (int)$data['status_id'] : 1,
            ];

            $this->movieModel->update($id, $movieData);

            // Cập nhật thể loại và quốc gia
            $this->CategoryModel->updateMovieCategories($id, $data['categories']);
            $this->CountryModel->updateMovieCountries($id, $data['countries']);

            // Xử lý xóa các tập phim cũ nếu có
            if (!empty($data['deleted_episodes'])) {
                $deletedIds = explode(',', $data['deleted_episodes']);
                foreach ($deletedIds as $episodeId) {
                    $this->EpisodeModel->delete($episodeId);
                }
            }

            // Cập nhật lại hoặc thêm mới các tập phim còn lại
            if (!empty($data['episodes'])) {
                $this->EpisodeModel->replaceAll($id, $data['episodes']);
            }

            header('Location: ' . $_ENV['BASE_URL'] . '/admin/movies');
            exit;
        }
    }

    public function uploadFile($file, $folder, $newName, $oldPath = null)
    {
        if ($file['error'] === 4) {

            return $oldPath;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $newName . '.' . $ext;
        $targetDir = dirname(__DIR__, 3) . "/public/img/{$folder}";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetPath = $targetDir . '/' . $filename;
        // Xóa ảnh cũ nếu có
        if ($oldPath) {
            $oldFullPath = dirname(__DIR__, 3) . "/public" . $oldPath;
            if (file_exists($oldFullPath)) {
                unlink($oldFullPath);
            }
        }
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Trả về đường dẫn để lưu vào CSDL
            return "/img/{$folder}/{$filename}";
        }
        return $oldPath;
    }
    public function delete($id)
    {
        $movie = $this->movieModel->find($id);
        $this->EpisodeModel->delete($id);

        $this->movieModel->removeAllCategories($id);
        $this->movieModel->removeAllCountries($id);
        if ($movie) {
            // Xóa các file ảnh nếu có
            $this->deleteFileIfExists($movie['poster_url']);
            $this->deleteFileIfExists($movie['thum_url']);
            $this->deleteFileIfExists($movie['banner_url']);

            // Xóa phim trong database
            $this->movieModel->delete($id);
        }


        header('Location: ' . $_ENV['BASE_URL'] . '/admin/movies');
        exit;
    }
    private function deleteFileIfExists($filePath)
    {
        if (!empty($filePath)) {

            $realPath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
            if (file_exists($realPath)) {
                unlink($realPath);
            }
        }
    }
    public function updateTag($movieId)
    {
        $selectedLabels = $_POST['tags'] ?? []; // Mảng label_id
        // Gọi model để cập nhật
        $this->TagModel->updateMovieTags($movieId, $selectedLabels);

        header('Location: ' . $_ENV['BASE_URL'] . '/admin/movies');
        exit;
    }


}
