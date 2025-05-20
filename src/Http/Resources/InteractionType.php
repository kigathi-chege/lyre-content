<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\InteractionType as InteractionTypeModel;
use Lyre\Resource;

class InteractionType extends Resource
{
    public function __construct(InteractionTypeModel $model)
    {
        parent::__construct($model);
    }
}
