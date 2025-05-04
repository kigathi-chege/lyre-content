<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Data as DataModel;
use Lyre\Resource;

class Data extends Resource
{
    public function __construct(DataModel $model)
    {
        parent::__construct($model);
    }
}
