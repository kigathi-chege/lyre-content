<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Data;
use Lyre\Content\Repositories\Contracts\DataRepositoryInterface;

class DataRepository extends Repository implements DataRepositoryInterface
{
    protected $model;

    public function __construct(Data $model)
    {
        parent::__construct($model);
    }
}
