<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Menu;
use Lyre\Content\Repositories\Contracts\MenuRepositoryInterface;

class MenuRepository extends Repository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }
}
