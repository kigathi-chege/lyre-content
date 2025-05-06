<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Section as SectionModel;
use Lyre\Resource;

class Section extends Resource
{
    public function __construct(SectionModel $model)
    {
        parent::__construct($model);
    }
}
