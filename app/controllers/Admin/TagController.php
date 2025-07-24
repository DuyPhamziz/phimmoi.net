<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;

class TagController extends BaseAdminController
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
        $tags = $this->TagModel->getAllTags();
        $this->render('admin/tags/index', compact('tags'));
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $name_tag = $_POST['name_tag'];
            $slug_tag = $_POST['slug_tag'];

            if ($name_tag && $slug_tag) {
                $this->TagModel->createTag($name_tag, $slug_tag);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/tags");
                exit;
            } else {
                die("Dữ liệu không hợp lệ!");
            }
        } else {
            die("Phương thức không hợp lệ!");
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $name_tag = $_POST['name_tag'];
            $slug_tag = $_POST['slug_tag'];

            if ($name_tag && $slug_tag) {
                $this->TagModel->updateTag($id, $name_tag, $slug_tag);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/tags");
                exit;
            } else {
                die("Dữ liệu không hợp lệ!");
            }
        } else {
            die("Phương thức không hợp lệ!");
        }
    }
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $this->TagModel->deleteTag($id);
            header("Location: " . $_ENV['BASE_URL'] . "/admin/tags");
            exit;
        } else {
            die("Phương thức không hợp lệ!");
        }
    }
}
