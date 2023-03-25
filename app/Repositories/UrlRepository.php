<?php

namespace App\Repositories;

use App\Contracts\UrlRepositoryInterface;
use App\Models\Url;

class UrlRepository implements UrlRepositoryInterface
{
    private $model;
    public function __construct(Url $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Url
    {
        return $this->model->create($data);
    }
}
