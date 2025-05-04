<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Page;
use Lyre\Content\Repositories\Contracts\PageRepositoryInterface;

class PageRepository extends Repository implements PageRepositoryInterface
{
    protected $model;

    public function __construct(Page $model)
    {
        parent::__construct($model);
    }
}
