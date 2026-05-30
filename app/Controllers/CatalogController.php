<?php

namespace App\Controllers;

use App\Services\CatalogService;

class CatalogController extends BaseController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /*
    |--------------------------------------------------------------------------
    | Homepage
    |--------------------------------------------------------------------------
    */
    public function home(): void
    {
        $this->requireLogin();

        $data = $this->catalogService->getHomePageData();

        extract($data);

        require BASE_PATH . '/view/home.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Catalog Page
    |--------------------------------------------------------------------------
    */
    public function index(): void
    {
        $this->requireLogin();

        $data = $this->catalogService->getCatalogPage($_GET);

        extract($data);

        require BASE_PATH . '/view/catalog.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Page (optional future use)
    |--------------------------------------------------------------------------
    */
    public function show(int $id): void
    {
        $this->requireLogin();

        $data = $this->catalogService->getById($id);

        if (!$data) {
            throw new \App\Exceptions\ValidationException([
                'catalog' => 'Item not found'
            ]);
        }

        require BASE_PATH . '/view/catalog-detail.php';
    }
}
