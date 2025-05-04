<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Text;
use Lyre\Content\Repositories\Contracts\TextRepositoryInterface;

class TextRepository extends Repository implements TextRepositoryInterface
{
    protected $model;

    public function __construct(Text $model)
    {
        parent::__construct($model);
    }
}
