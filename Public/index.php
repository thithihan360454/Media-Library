<?php

declare(strict_types=1);

use Dotenv\Dotenv;

use App\Core\Database;
use App\Core\ErrorHandler;
use App\Core\ExceptionHandler;

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
| BASE PATH
|--------------------------------------------------------------------------
*/

define('BASE_PATH', dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| AUTOLOAD
|--------------------------------------------------------------------------
*/
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/inc/CustomPath.php';

/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| ENVIRONMENT
|--------------------------------------------------------------------------
*/
$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

/*
|--------------------------------------------------------------------------
| ERROR REPORTING
|--------------------------------------------------------------------------
*/
error_reporting(E_ALL);

ini_set(
    'display_errors',
    $_ENV['APP_DEBUG'] ?? '1'
);

ini_set('html_errors', '1');

/*
|--------------------------------------------------------------------------
| GLOBAL ERROR HANDLER
|--------------------------------------------------------------------------
*/
set_error_handler(
    [ErrorHandler::class, 'handle']
);

set_exception_handler(
    [ExceptionHandler::class, 'handle']
);

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

$formatRepo = new FormatRepository($db);

$userRepo = new UserRepository($db);

/*
|--------------------------------------------------------------------------
| VALIDATOR
|--------------------------------------------------------------------------
*/
$validator = new Validator();

/*
|--------------------------------------------------------------------------
| SERVICES
|--------------------------------------------------------------------------
*/
$catalogService = new CatalogService(
    $catalogRepo
);

$formatService = new FormatService(
    $formatRepo
);

$userService = new UserService(
    $userRepo,
    $validator
);

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
$catalogController = new CatalogController(
    $catalogService
);

$detailsController = new DetailsController(
    $catalogService
);

$suggestController = new SuggestController(
    $formatService
);

$authController = new AuthController(
    $userService
);

/*
|--------------------------------------------------------------------------
| API CONTROLLERS
|--------------------------------------------------------------------------
*/
$catalogApiController = new CatalogApiController(
    $catalogService
);

$detailsApiController = new DetailsApiController(
    $catalogService
);

$suggestApiController = new SuggestApiController(
    $formatService
);

$authApiController = new AuthApiController(
    $userService
);

/*
|--------------------------------------------------------------------------
| CURRENT PAGE
|--------------------------------------------------------------------------
*/
$page = $_GET['page'] ?? 'home';

/*
|--------------------------------------------------------------------------
| AUTH MIDDLEWARE
|--------------------------------------------------------------------------
*/
$protectedPages = [
    'home',
    'catalog',
    'details',
    'suggest'
];

if (
    in_array($page, $protectedPages, true)
    && !isset($_SESSION['userid'])
) {

    $_SESSION['auth_error'] =
        'Please login first';

    header(
        'Location: index.php?page=login'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
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
        $catalogApiController->index();
        break;

    case 'api-details':
        $detailsApiController->show();
        break;

    case 'api-suggest':
        $suggestApiController->store();
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
    | 404
    |--------------------------------------------------------------------------
    */
    default:
        throw new \App\Exceptions\NotFoundException('Page not found');
}
