<?php

namespace App\Contracts;

use App\Models\Url;

interface UrlRepositoryInterface
{
    public function create(array $data): Url;
}
