<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Button;
use Lyre\Content\Repositories\Contracts\ButtonRepositoryInterface;

class ButtonRepository extends Repository implements ButtonRepositoryInterface
{
    protected $model;

    public function __construct(Button $model)
    {
        parent::__construct($model);
    }
}
