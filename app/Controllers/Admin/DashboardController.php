<?php

namespace App\controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Tag;
class DashboardController extends BaseAdminController
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
        $countCategory = $this->movieModel->countCategory();
        $countComment = $this->movieModel->countComment();
        $countCountry = $this->movieModel->countCountry();
        $countEpisode = $this->movieModel->countEpisode();
        $countMovie = $this->movieModel->countMovie();
        $countUser = $this->movieModel->countUser();
        $countMovie = $this->movieModel->countTag();
        
        $this->render('admin/dashboard', [
            'page_title' => 'Tổng quan',
            'user' => $_SESSION['user'] ?? 'admin',
            'countCategory' => $countCategory['cnt'] ?? 0,
            'countComment' => $countComment['cnt'] ?? 0,
            'countCountry' => $countCountry['cnt'] ?? 0,
            'countEpisode' => $countEpisode['cnt'] ?? 0,
            'countMovie' => $countMovie['cnt'] ?? 0,
            'countUser' => $countUser['cnt'] ?? 0,
            'countCategory' => $countCategory['cnt'] ?? 0,
        ]);
    }
    
}
