<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Icon;
use Lyre\Content\Repositories\Contracts\IconRepositoryInterface;

class IconRepository extends Repository implements IconRepositoryInterface
{
    protected $model;

    public function __construct(Icon $model)
    {
        parent::__construct($model);
    }
}
