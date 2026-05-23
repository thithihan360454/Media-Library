<?php

namespace App\Interfaces;

interface FormatRepositoryInterface
{
    public function get_format_drop_down($category = null);

    public function get_category_drop_down();

    public function get_genres_drop_down($category = null);
}
