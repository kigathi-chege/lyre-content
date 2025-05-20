<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Interaction as InteractionModel;
use Lyre\Resource;

class Interaction extends Resource
{
    public function __construct(InteractionModel $model)
    {
        parent::__construct($model);
    }
}
