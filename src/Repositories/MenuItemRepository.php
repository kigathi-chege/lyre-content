<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\MenuItem;
use Lyre\Content\Repositories\Contracts\MenuItemRepositoryInterface;

class MenuItemRepository extends Repository implements MenuItemRepositoryInterface
{
    protected $model;

    public function __construct(MenuItem $model)
    {
        parent::__construct($model);
    }
}
