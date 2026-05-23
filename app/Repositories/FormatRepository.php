<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\FormatRepositoryInterface;

class FormatRepository implements FormatRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Get formats based on selected category
    function get_format_drop_down($category = null)
    {
        $result = $this->db->prepare(" CALL sp_get_formats_by_category (:category)");

        $result->bindValue(
            ':category',
            $category,
            $category === null ? PDO::PARAM_NULL : PDO::PARAM_STR
        );

        $result->execute();

        $format = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $format[$row["category"]][] = $row["format"];
        }

        $result->closeCursor();

        return $format;
    }

    // Get all unique categories
    function get_category_drop_down()
    {
        $sql = " SELECT DISTINCT category FROM view_catalog ORDER BY category";

        $result = $this->db->prepare($sql);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_COLUMN);
    }

    // Get genres based on selected category
    function get_genres_drop_down($category = null)
    {
        $result = $this->db->prepare(" CALL sp_get_genres_by_category (:category)");

        $result->bindValue(
            ':category',
            $category,
            $category === null ? PDO::PARAM_NULL : PDO::PARAM_STR
        );

        $result->execute();

        $genre = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $genre[$row["category"]][] = $row["genre"];
        }

        $result->closeCursor();

        return $genre;
    }
}
