<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\SectionButton as SectionButtonModel;
use Lyre\Resource;

class SectionButton extends Resource
{
    public function __construct(SectionButtonModel $model)
    {
        parent::__construct($model);
    }
}
