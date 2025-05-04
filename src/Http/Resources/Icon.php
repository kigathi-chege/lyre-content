<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Icon as IconModel;
use Lyre\Resource;

class Icon extends Resource
{
    public function __construct(IconModel $model)
    {
        parent::__construct($model);
    }
}
