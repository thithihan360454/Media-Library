<?php

use Dotenv\Dotenv;

use App\Core\Database;

use App\Repositories\CatalogRepository;
use App\Repositories\FormatRepository;
use App\Repositories\UserRepository;

use App\Services\CatalogService;
use App\Services\FormatService;
use App\Services\UserService;

use App\Controllers\CatalogController;
use App\Controllers\DetailsController;
use App\Controllers\SuggestController;
use App\Controllers\AuthController;

use App\Controllers\Api\CatalogApiController;
use App\Controllers\Api\DetailsApiController;
use App\Controllers\Api\SuggestApiController;
use App\Controllers\Api\AuthApiController;

use App\Validation\Validator;

/*
|--------------------------------------------------------------------------
| ERROR REPORTING
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

/*
|--------------------------------------------------------------------------
| SESSION START (CRITICAL)
|--------------------------------------------------------------------------
*/
session_start();

/*
|--------------------------------------------------------------------------
| BASE PATH
|--------------------------------------------------------------------------
*/
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/App/Core/Database.php';
require_once BASE_PATH . '/inc/CustomPath.php';

/*
|--------------------------------------------------------------------------
| ENVIRONMENT
|--------------------------------------------------------------------------
*/
$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/
$db = Database::getConnection();

/*
|--------------------------------------------------------------------------
| REPOSITORIES
|--------------------------------------------------------------------------
*/
$catalogRepo = new CatalogRepository($db);
$formatRepo  = new FormatRepository($db);
$userRepo    = new UserRepository($db);

/*
|--------------------------------------------------------------------------
| SERVICES
|--------------------------------------------------------------------------
*/
$catalogService = new CatalogService($catalogRepo);
$formatService  = new FormatService($formatRepo);
// $userService    = new UserService($userRepo);
$validator = new Validator();

$userService = new UserService(
    $userRepo,
    $validator
);

/*
|--------------------------------------------------------------------------
| CONTROLLERS (CREATE ONCE ONLY)
|--------------------------------------------------------------------------
*/
$catalogController = new CatalogController($catalogService);
$detailsController = new DetailsController($catalogService);
$suggestController = new SuggestController($formatService);
$authController    = new AuthController($userService);

/*
|--------------------------------------------------------------------------
| API CONTROLLERS (CREATE ONCE ONLY)
|--------------------------------------------------------------------------
*/

$authApiController = new AuthApiController($userService);

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
$page = $_GET['page'] ?? 'home';


/*
|----------------------------------------------------------------
| AUTH PROTECTION (PUT HERE)
|----------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| AUTH MIDDLEWARE (FIXED)
|--------------------------------------------------------------------------
*/
$protectedPages = ['home', 'catalog', 'details', 'suggest'];

if (
    in_array($page, $protectedPages) &&
    !isset($_SESSION['user_id'])
) {
    $_SESSION['auth_error'] = "Please login first!";

    header("Location: index.php?page=login");
    exit;
}

switch ($page) {


    /*
    |--------------------------------------------------------------------------
    | WEB ROUTES
    |--------------------------------------------------------------------------
    */
    case 'home':
        $catalogController->home();
        break;

    case 'catalog':
        $catalogController->index();
        break;

    case 'details':
        $detailsController->show();
        break;

    case 'suggest':
        $suggestController->index();
        break;

    /*
    |--------------------------------------------------------------------------
    | AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    case 'register':
        $authController->showRegister();
        break;

    case 'register-submit':
        $authController->register();
        break;

    case 'login':
        $authController->showLogin();
        break;

    case 'login-submit':
        $authController->login();
        break;

    case 'logout':
        $authController->logout();
        break;

    /*
    |--------------------------------------------------------------------------
    | API ROUTES
    |--------------------------------------------------------------------------
    */
    case 'api-catalog':
        $controller = new CatalogApiController($catalogService);
        $controller->index();
        break;

    case 'api-details':
        $controller = new DetailsApiController($catalogService);
        $controller->show();
        break;

    case 'api-suggest':
        $controller = new SuggestApiController($formatService);
        $controller->store();
        break;

    case 'api-register':
        $authApiController->register();
        break;

    case 'api-login':
        $authApiController->login();
        break;

    case 'api-logout':
        $authApiController->logout();
        break;


    /*
    |--------------------------------------------------------------------------
    | DEFAULT ROUTE
    |--------------------------------------------------------------------------
    */
    default:
        $catalogController->home();
        break;
}
