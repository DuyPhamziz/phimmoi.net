<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Core\Twig;

class BaseController
{
    public function __construct()
    {
        $categoryModel = new \App\Models\Category();
        $countryModel = new \App\Models\Country();
        $tagsModel     = new \App\Models\Tag(); 

        Twig::addGlobal('categories', $categoryModel->getAll());
        Twig::addGlobal('countries', $countryModel->getAll());
        Twig::addGlobal('tags', $tagsModel->getAll());

    }
}
