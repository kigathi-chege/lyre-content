<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Button as ButtonModel;
use Lyre\Resource;

class Button extends Resource
{
    public function __construct(ButtonModel $model)
    {
        parent::__construct($model);
    }
}
