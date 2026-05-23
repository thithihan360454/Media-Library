<?php

namespace App\Controllers\Api;

use App\Services\CatalogService;
use Exception;
// require_once BASE_PATH . '/Model/Service/CatalogService.php';

class DetailsApiController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /*
     * GET /api/details?id=1
     */
    public function show(): void
    {
        header('Content-Type: application/json');

        // Validate ID
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Invalid ID
        if (!$id) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid item ID'
            ]);

            return;
        }

        // Get item
        $item = $this->catalogService->getById($id);

        // Item not found
        if (empty($item)) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Item not found'
            ]);

            return;
        }

        // Success response
        echo json_encode([
            'success' => true,
            'data' => $item
        ]);
    }
}
