<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;

class CountryController extends BaseAdminController
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
        $countries = $this->CountryModel->getAllCountries();
        $this->render('admin/countries/index', compact('countries'));
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $name = $_POST['name'];
            $slug = $_POST['slug'];

            if ($name && $slug) {
                $this->CountryModel->createCountry($name, $slug);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/countries");
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
            $name = $_POST['name'];
            $slug = $_POST['slug'];

            if ($name && $slug) {
                $this->CountryModel->updateCountry($id, $name, $slug);
                header("Location: " . $_ENV['BASE_URL'] . "/admin/countries");
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
            $this->CountryModel->deleteCountry($id);
            header("Location: " . $_ENV['BASE_URL'] . "/admin/countries");
            exit;
        } else {
            die("Phương thức không hợp lệ!");
        }
    }
}
