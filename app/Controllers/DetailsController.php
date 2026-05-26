<?php

namespace App\Controllers;

use App\Services\CatalogService;

/**
 * Handles displaying detailed information
 * for a single catalog item.
 */

class DetailsController extends BaseController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        // Inject catalog service dependency
        $this->catalogService = $catalogService;
    }

    // Show item details page
    public function show()
    {
        $this->requireLogin();
        // Validate item ID from URL
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Redirect if ID is invalid
        if (!$id) {
            header("Location: " . BASE_URL . "/Public/index.php?page=catalog");
            exit;
        }

        // Get item data from service
        // $item = $this->CatalogService->single_item_array($id);
        $item = $this->catalogService->getById($id);
        // Redirect if item does not exist
        if (empty($item)) {
            header("Location: " . BASE_URL . "/Public/index.php?page=catalog");
            exit;
        }

        // Page information
        $pageTitle = $item['title'];
        $section   = $item['category'];

        // Load details view
        require BASE_PATH . '/view/details.php';
    }
}
