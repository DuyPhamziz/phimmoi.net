<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;

class CategoryController extends BaseAdminController {
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
    public function index() {
        $categories = $this->CategoryModel->getAllCategories();
        
        $this->render('admin/categories/index', [
            'categories' => $categories,
        ]);
    }
    public function create() {
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $name_cat = $_POST['name_cat'];
            $slug_cat = $_POST['slug_cat'];

            if($name_cat && $slug_cat) {
                $this->CategoryModel->createCategory($name_cat, $slug_cat);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/categories");
                exit;
            }else {
                die("Dữ liệu không hợp lệ!");
            }
        }else {
            die("Phương thức không hợp lệ!");
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $name_cat = $_POST['name_cat'];
            $slug_cat = $_POST['slug_cat'];

            if ($name_cat && $slug_cat) {
                $this->CategoryModel->updateCategory($id, $name_cat, $slug_cat);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/categories");
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
            $this->CategoryModel->deleteCategory($id);
            header("Location: " . $_ENV['BASE_URL'] . "/admin/categories");
            exit();
        } else {
            die("Phương thức không hợp lệ");
        }
    }
}