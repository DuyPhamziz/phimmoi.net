<?php

// Load autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Bramus\Router\Router;
use App\Core\Twig;
use App\Controllers\AuthController;
use App\Controllers\MovieController;
use App\Controllers\HistoryController;
use App\Controllers\CommentController;
use App\Controllers\Admin\DashboardController;

// Load environment variables
$envPath = dirname(__DIR__);
$dotenv = Dotenv::createImmutable($envPath);
$dotenv->safeLoad();

// Start session if none
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize Twig and global vars
$twig = Twig::getEnvironment();
$twig->addGlobal('categories', (new \App\Models\Category())->getAll());
$twig->addGlobal('tags', (new \App\Models\Tag())->getAll());
$twig->addGlobal('countries', (new \App\Models\Country())->getAll());

// Initialize router
$router = new Router();

// === AUTH ===
$router->get('/login', 'App\Controllers\AuthController@login');
$router->get('/logout', 'App\Controllers\AuthController@logout');
$router->get('/auth/callback', 'App\Controllers\AuthController@callback');

// === ADMIN MIDDLEWARE ===
$router->before('GET', '/admin.*', function() {
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        header('Location: /login');
        exit;
    }
});

// === ADMIN ROUTES ===
$router->get('/admin', 'App\Controllers\Admin\DashboardController@index');

// Movie admin
$router->get('/admin/movies', 'App\Controllers\Admin\MovieController@index');
$router->get('/admin/movies/show/{id}', 'App\Controllers\Admin\MovieController@show');
$router->get('/admin/movies/create', 'App\Controllers\Admin\MovieController@create');
$router->post('/admin/movies/store', 'App\Controllers\Admin\MovieController@store');
$router->get('/admin/movies/edit/{id}', 'App\Controllers\Admin\MovieController@edit');
$router->post('/admin/movies/update/{id}', 'App\Controllers\Admin\MovieController@update');
$router->post('/admin/movies/delete/{id}', 'App\Controllers\Admin\MovieController@delete');
$router->get('/admin/movies/tag/{id}', 'App\Controllers\Admin\MovieController@tag');
$router->post('/admin/movies/tag/{id}', 'App\Controllers\Admin\MovieController@updateTag');

// Episode admin
$router->get('/admin/episodes', 'App\Controllers\Admin\EpisodeController@index');
$router->post('/admin/episodes/create', 'App\Controllers\Admin\EpisodeController@create');
$router->post('/admin/episodes/update/{id}', 'App\Controllers\Admin\EpisodeController@update');
$router->post('/admin/episodes/delete/{id}', 'App\Controllers\Admin\EpisodeController@delete');

// Category admin
$router->get('/admin/categories', 'App\Controllers\Admin\CategoryController@index');
$router->post('/admin/categories/create', 'App\Controllers\Admin\CategoryController@create');
$router->post('/admin/categories/update/{id}', 'App\Controllers\Admin\CategoryController@update');
$router->post('/admin/categories/delete/{id}', 'App\Controllers\Admin\CategoryController@delete');

// Country admin
$router->get('/admin/countries', 'App\Controllers\Admin\CountryController@index');
$router->post('/admin/countries/create', 'App\Controllers\Admin\CountryController@create');
$router->post('/admin/countries/update/{id}', 'App\Controllers\Admin\CountryController@update');
$router->post('/admin/countries/delete/{id}', 'App\Controllers\Admin\CountryController@delete');

// Tag admin
$router->get('/admin/tags', 'App\Controllers\Admin\TagController@index');
$router->post('/admin/tags/create', 'App\Controllers\Admin\TagController@create');
$router->post('/admin/tags/update/{id}', 'App\Controllers\Admin\TagController@update');
$router->post('/admin/tags/delete/{id}', 'App\Controllers\Admin\TagController@delete');

// === USER ROUTES ===
$router->get('/', 'App\Controllers\MovieController@home');
$router->get('/search', 'App\Controllers\MovieController@search');
$router->get('/search/([^/]+)', 'App\Controllers\MovieController@searchSlug');
$router->get('/history', 'App\Controllers\HistoryController@index');
$router->get('/loc/([^/]+)', 'App\Controllers\MovieController@filter');
$router->post('/history/save-progress', 'App\Controllers\HistoryController@saveProgress');
$router->post('/comment', 'App\Controllers\MovieController@handleCommentPost');
$router->get('/{slug}/{ep}', 'App\Controllers\MovieController@watch');
$router->get('/{slug}', 'App\Controllers\MovieController@info');

// Run router
$router->run();
