<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\SectionSection;
use Lyre\Content\Repositories\Contracts\SectionSectionRepositoryInterface;

class SectionSectionRepository extends Repository implements SectionSectionRepositoryInterface
{
    protected $model;

    public function __construct(SectionSection $model)
    {
        parent::__construct($model);
    }
}
