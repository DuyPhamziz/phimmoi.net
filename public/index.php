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
$router->get('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/auth/callback', [AuthController::class, 'callback']);

// === ADMIN MIDDLEWARE ===
$router->before('GET', '/admin.*', function() {
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        header('Location: /login');
        exit;
    }
});

// === ADMIN ROUTES ===
$router->get('/admin', [DashboardController::class, 'index']);
// Movie admin
$router->mount('/admin/movies', function() use ($router) {
    $router->get('/', ['App\Controllers\Admin\MovieController', 'index']);
    $router->get('/show/{id}', ['App\Controllers\Admin\MovieController', 'show']);
    $router->get('/create', ['App\Controllers\Admin\MovieController', 'create']);
    $router->post('/store', ['App\Controllers\Admin\MovieController', 'store']);
    $router->get('/edit/{id}', ['App\Controllers\Admin\MovieController', 'edit']);
    $router->post('/update/{id}', ['App\Controllers\Admin\MovieController', 'update']);
    $router->post('/delete/{id}', ['App\Controllers\Admin\MovieController', 'delete']);
    $router->get('/tag/{id}', ['App\Controllers\Admin\MovieController', 'tag']);
    $router->post('/tag/{id}', ['App\Controllers\Admin\MovieController', 'updateTag']);
});

// Repeat similar mount for episodes, categories, countries, tags as needed...

// === USER ROUTES ===
$router->get('/', [MovieController::class, 'home']);
$router->get('/search', [MovieController::class, 'search']);
$router->get('/search/{slug}', [MovieController::class, 'searchSlug']);
$router->get('/history', [HistoryController::class, 'index']);
$router->get('/loc/{slug}', [MovieController::class, 'filter']);
$router->post('/history/save-progress', [HistoryController::class, 'saveProgress']);
$router->post('/comment', [CommentController::class, 'handleCommentPost']);
$router->get('/{slug}/{ep}', [MovieController::class, 'watch']);
$router->get('/{slug}', [MovieController::class, 'info']);

// Run router
$router->run();
