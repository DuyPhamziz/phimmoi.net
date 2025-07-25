<?php

namespace App\Controllers;

use App\Core\Helpers;
use App\Models\Tag;
use App\Models\Movie;

class CategoryController
{
    protected $tagModel;
    protected $movieModel;

    public function __construct()
    {
        $this->tagModel = new Tag(); // Model bảng tags
        $this->movieModel = new Movie(); // Model bảng movies
    }

    public function show($slug)
    {
        // Lấy thể loại theo slug
        $category = $this->tagModel->getTagBySlug($slug);

        if (!$category) {
            return Helpers::render404("Không tìm thấy thể loại");
        }

        // Lấy danh sách phim theo ID tag
        $movies = $this->movieModel->getMoviesByTag($category->id);

        // Render trang filter
        Helpers::render('filter', [
            'category' => $category,
            'movies' => $movies,
            'slug' => $slug   
        ]);
    }
}
