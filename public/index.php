<?php


require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$envPath = __DIR__ . '/../';
if (file_exists($envPath . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
}



use Bramus\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\MovieController;
use App\Controllers\HistoryController;
use App\Controllers\CommentController;
use App\Controllers\Admin\BaseAdminController;

use App\Models\Category;
use App\Models\Country;
use App\Models\Tag;
use App\Core\Twig;

// Twig::getEnvironment()->addGlobal('categories', (new Category())->getAll());
// Twig::getEnvironment()->addGlobal('tags', (new Tag())->getAll());
// Twig::getEnvironment()->addGlobal('countries', (new Country())->getAll());



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load biến môi trường
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load biến môi trường
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Khởi tạo router
$router = new Router();

// ==== AUTH ====
$router->get('/login', fn() => (new AuthController)->login());

$router->get('/logout', fn() => (new AuthController)->logout());
$router->get('/auth/callback', fn() => (new AuthController)->callback());

// ==== ADMIN ====
// ==== Middleware bảo vệ admin ====
$router->before('GET', '/admin.*', function () {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        header('Location: /');
        exit;
    }
});

// ==== Route ADMIN ====
$router->get('/admin', fn() => (new \App\Controllers\Admin\DashboardController)->index());
$router->get('/admin/movies', 'App\controllers\Admin\MovieController@index');
$router->get('/admin/movies/show/{id}', 'App\controllers\Admin\MovieController@show');
$router->get('/admin/movies/create', 'App\controllers\Admin\MovieController@create');
$router->post('/admin/movies/store', 'App\controllers\Admin\MovieController@store');
$router->get('/admin/movies/edit/{id}', 'App\controllers\Admin\MovieController@edit');
$router->post('/admin/movies/update/{id}', 'App\controllers\Admin\MovieController@update');
$router->post('/admin/movies/delete/{id}', 'App\controllers\Admin\MovieController@delete');
$router->get('/admin/movies/tag/{id}', 'App\controllers\Admin\MovieController@tag');
$router->post('/admin/movies/tag/{id}', 'App\controllers\Admin\MovieController@updateTag');

$router->get('/admin/episodes', 'App\controllers\Admin\EpisodeController@index');
$router->post('/admin/episodes/create', 'App\controllers\Admin\EpisodeController@create');
$router->post('/admin/episodes/update/{id}', 'App\controllers\Admin\EpisodeController@update');
$router->post('/admin/episodes/delete/{id}', 'App\controllers\Admin\EpisodeController@delete');

$router->get('/admin/categories', 'App\controllers\Admin\CategoryController@index');
$router->post('/admin/categories/create', 'App\controllers\Admin\CategoryController@create');
$router->post('/admin/categories/update/{id}', 'App\controllers\Admin\CategoryController@update');
$router->post('/admin/categories/delete/{id}', 'App\controllers\Admin\CategoryController@delete');

$router->get('/admin/countries', 'App\controllers\Admin\CountryController@index');
$router->post('/admin/countries/create', 'App\controllers\Admin\CountryController@create');
$router->post('/admin/countries/update/{id}', 'App\controllers\Admin\CountryController@update');
$router->post('/admin/countries/delete/{id}', 'App\controllers\Admin\CountryController@delete');


$router->get('/admin/tags', 'App\controllers\Admin\TagController@index');
$router->post('/admin/tags/create', 'App\controllers\Admin\TagController@create');
$router->post('/admin/tags/update/{id}', 'App\controllers\Admin\TagController@update');
$router->post('/admin/tags/delete/{id}', 'App\controllers\Admin\TagController@delete');


// ==== ROUTES NGƯỜI DÙNG ====
$router->get('/', fn() => (new MovieController)->home());
$router->get('/search', fn() => (new MovieController)->search());
$router->get('/search/([^/]+)', fn($slug) => (new MovieController)->searchSlug($slug));

$router->get('/history', 'App\controllers\HistoryController@index');

// ROUTE CHO THỂ LOẠI VÀ QUỐC GIA 

$router->get('/loc/([^/]+)', 'App\Controllers\MovieController@filter');


// $router->get('/tag/([^/]+)', 'App\Controllers\MovieController@filter');
// $router->get('/categories/([^/]+)', 'App\Controllers\MovieController@filter');

// ROUTE ĐỘNG PHẢI ĐỂ SAU CÙNG
$router->get('/{slug}/{ep}', fn($slug, $ep) => (new MovieController)->watch($slug, $ep));
$router->post('/history/save-progress', fn() => (new HistoryController)->saveProgress());


// Route xử lý gửi bình luận

$router->post('/comment', 'App\controllers\MovieController@handleCommentPost');
$router->get('/{slug}', fn($slug) => (new MovieController)->info($slug));


// ==== RUN ROUTER ====
$router->run();