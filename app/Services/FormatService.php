<?php

namespace  App\Services;
use App\Interfaces\FormatRepositoryInterface;
use App\Repositories\FormatRepository;

use App\Core\Database;

/**
 * Handles format-related business logic and manages
 * communication between controllers and repositories.
 */

class FormatService
{
    private FormatRepositoryInterface $repo;

    public function __construct(?FormatRepositoryInterface $repo = null)
    {
        // Create default repository if none is provided
        if ($repo === null) {
            $db = Database::getConnection();
            $repo = new FormatRepository($db);
        }

        $this->repo = $repo;
    }

    // Get format dropdown data
    function format_array($category = null)
    {
        return $this->repo->get_format_drop_down($category);
    }

    // Get category dropdown data
    function category_drop_down()
    {
        return $this->repo->get_category_drop_down();
    }

    // Get genres dropdown data
    function genres_array($category = null)
    {
        return $this->repo->get_genres_drop_down($category);
    }
}