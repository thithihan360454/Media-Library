<?php

namespace App\Controllers\Api;

use App\Services\CatalogService;
use Exception;

// require_once BASE_PATH . '/Model/Service/CatalogService.php';

/*
 * API Controller:
 * Returns catalog data as JSON
 */

class CatalogApiController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /*
     * GET /api-catalog
     *
     * Examples:
     *
     * All items:
     * ?page=api-catalog
     *
     * Books only:
     * ?page=api-catalog&section=books
     *
     * Movies only:
     * ?page=api-catalog&section=movies
     *
     * Music only:
     * ?page=api-catalog&section=music
     *
     * Search:
     * ?page=api-catalog&search=harry
     *
     * Books + search:
     * ?page=api-catalog&section=books&search=clean
     */

    public function index(): void
    {
        header('Content-Type: application/json');

        try {

            /*
             * Get catalog data from service
             */

            $data = $this->catalogService->getCatalogPage($_GET);

            /*
             * Total items count
             */

            $count = 0;

            if (isset($data['catalog']) && is_array($data['catalog'])) {
                $count = count($data['catalog']);
            }

            /*
             * Success response
             */

            http_response_code(200);

            echo json_encode([
                'success' => true,

                'filters' => [
                    'section' => $_GET['section'] ?? null,
                    'search'  => $_GET['search'] ?? null,
                ],

                'count' => $count,

                'data' => $data
            ], JSON_PRETTY_PRINT);
        } catch (Exception $e) {

            /*
             * Error response
             */

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }
}
