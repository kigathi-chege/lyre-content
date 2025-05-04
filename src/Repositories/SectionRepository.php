<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Section;
use Lyre\Content\Repositories\Contracts\SectionRepositoryInterface;

class SectionRepository extends Repository implements SectionRepositoryInterface
{
    protected $model;

    public function __construct(Section $model)
    {
        parent::__construct($model);
    }
}
