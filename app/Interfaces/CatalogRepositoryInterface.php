<?php
namespace App\Interfaces;
/**
 * Defines methods for retrieving catalog data
 * from the data source.
 */

interface CatalogRepositoryInterface extends BaseInterface
{
    // Get total catalog item count
    // public function getCatalogCount($category = null, $search = null);

    // Get complete catalog list
    // public function getFullCatalog($limit = null, $offset = 0);

    // Get catalog items by category
    public function getCategoryCatalog($category, $limit = null, $offset = 0);

    // Search catalog items by keyword and category
    public function getSearchCatalog($search, $category = null, $limit = null, $offset = 0);

    // Get random catalog items
    public function getRandomCatalog();

    // Get a single catalog item by ID
    // public function getSingleItem($id);
}