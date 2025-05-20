<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\InteractionType;
use Lyre\Content\Repositories\Contracts\InteractionTypeRepositoryInterface;

class InteractionTypeRepository extends Repository implements InteractionTypeRepositoryInterface
{
    protected $model;

    public function __construct(InteractionType $model)
    {
        parent::__construct($model);
    }

    public function all(array | null $callbacks = [], $paginate = true)
    {
        $callbacks[] = fn($query) => $query->where('status', 'active');
        return parent::all($callbacks, $paginate);
    }
}
