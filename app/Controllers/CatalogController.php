<?php

namespace App\Controllers;

use App\Services\CatalogService;
//require_once BASE_PATH . '/Service/CatalogService.php';

/*
 * Thin Controller:
 * Only handles HTTP request → passes to service → loads view
 */

class CatalogController extends BaseController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /*
     * Homepage
     */
    public function home(): void
    {
        $this->requireLogin();
        $data = $this->catalogService->getHomePageData();

        // Extract variables for view
        extract($data);

        require BASE_PATH . '/view/home.php';
    }

    /**
     * Catalog page (FULL LOGIC MOVED TO SERVICE)
     */
    public function index(): void
    {
        $this->requireLogin();
        $data = $this->catalogService->getCatalogPage($_GET);

        // Make variables available in view
        extract($data);

        require BASE_PATH . '/view/catalog.php';
    }
}
