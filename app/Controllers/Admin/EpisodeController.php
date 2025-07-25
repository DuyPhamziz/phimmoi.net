<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;

class EpisodeController extends BaseAdminController {
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
        $episodes = $this->EpisodeModel->getAllWithMovieName();

        
        $groupedEpisodes = [];

        foreach ($episodes as $ep) {
            $movieName = $ep['movie_name']; 
            if (!isset($groupedEpisodes[$movieName])) {
                $groupedEpisodes[$movieName] = [];
            }
            $groupedEpisodes[$movieName][] = $ep;
        }

        $this->render('admin/episodes/index', [
            'groupedEpisodes' => $groupedEpisodes
        ]);
    }
    public function create() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movie_id = $_POST['movie_id'] ?? null;
            $episode_number = $_POST['episode_number'] ?? null;
            $title = $_POST['title'] ?? null;
            $link_m3u8 = $_POST['link_m3u8'] ?? null;

            if ($movie_id && $episode_number && $title && $link_m3u8) {
                $data = [
                    'movie_id' => $movie_id,
                    'episode_number' => $episode_number,
                    'title' => $title,
                    'link_m3u8' => $link_m3u8
                ];
            
                $this->EpisodeModel->createEpisode($data);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/episodes");
                exit();
            }else {
                die("Dữ liệu không hợp lệ!");
            }
        }else {
            die('Phương thức không hợp lệ!');
        }
    }
    public function update($id) {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movie_id = $_POST['movie_id'] ?? null;
            $episode_number = $_POST['episode_number'] ?? null;
            $title = $_POST['title'] ?? null;
            $link_m3u8 = $_POST['link_m3u8'] ?? null;
            
            if($movie_id && $episode_number && $title && $link_m3u8) {
                $data = [
                    'movie_id' => $movie_id,
                    'episode_number' => $episode_number,
                    'title' => $title,
                    'link_m3u8' => $link_m3u8
                ];

               
                $this->EpisodeModel->updateEpisode($id ,$data);

                header("Location: " . $_ENV['BASE_URL'] . "/admin/episodes");
                exit();

            }else {
                die("Dữ liệu không hợp lệ!");
            }
        }else {
            die("Phương thức không hợp lệ!");
        }
    }
    public function delete($id) {
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $this->EpisodeModel->deleteEpisode($id);
            header("Location: " . $_ENV['BASE_URL'] . "/admin/episodes");
            exit();

        }else {
            die("Phương thức không hợp lệ");
        }
    }
}