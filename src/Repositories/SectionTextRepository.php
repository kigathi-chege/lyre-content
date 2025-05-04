<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\SectionText;
use Lyre\Content\Repositories\Contracts\SectionTextRepositoryInterface;

class SectionTextRepository extends Repository implements SectionTextRepositoryInterface
{
    protected $model;

    public function __construct(SectionText $model)
    {
        parent::__construct($model);
    }
}
