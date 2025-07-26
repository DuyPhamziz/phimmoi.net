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

class HistoryController extends BaseController
{
    protected $historyModel;
    protected $movieModel;
    protected $TagModel;
    protected $CountryModel;
    protected $CategoryModel;
    protected $commentModel;
    public function __construct()
    {
        parent::__construct();
        $this->historyModel = new History();
        $this->movieModel = new Movie();
        $this->commentModel = new Comment();
        $this->TagModel = new Tag();
        $this->CountryModel = new Country();
        $this->CategoryModel = new Category();
    }
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $histories = $this->historyModel->getHistory($userId);

        Helpers::render('history', [
            'histories' => $histories,
            'countries' => $this->CountryModel->getAll(),
            'categories' => $this->CategoryModel->getAll()
        ]);
    }


    public function saveProgress()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user']['id'];
        $movieId = $data['movie_id'] ?? null;
        $episodeId = $data['episode_id'] ?? null;
        $progress = $data['progress_seconds'] ?? 0;

        if (!$movieId || !$episodeId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing movie_id or episode_id']);
            return;
        }

        $historyModel = new History();
        $result = $historyModel->saveOrUpdateProgress($userId, $movieId, $episodeId, $progress);

        echo json_encode(['status' => $result ? 'ok' : 'error']);
    }
    
}
