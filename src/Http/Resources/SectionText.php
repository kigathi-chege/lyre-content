<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\SectionText as SectionTextModel;
use Lyre\Resource;

class SectionText extends Resource
{
    public function __construct(SectionTextModel $model)
    {
        parent::__construct($model);
    }
}
