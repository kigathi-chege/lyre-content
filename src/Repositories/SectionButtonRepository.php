<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\SectionButton;
use Lyre\Content\Repositories\Contracts\SectionButtonRepositoryInterface;

class SectionButtonRepository extends Repository implements SectionButtonRepositoryInterface
{
    protected $model;

    public function __construct(SectionButton $model)
    {
        parent::__construct($model);
    }
}
