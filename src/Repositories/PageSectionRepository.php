<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\PageSection;
use Lyre\Content\Repositories\Contracts\PageSectionRepositoryInterface;

class PageSectionRepository extends Repository implements PageSectionRepositoryInterface
{
    protected $model;

    public function __construct(PageSection $model)
    {
        parent::__construct($model);
    }
}
