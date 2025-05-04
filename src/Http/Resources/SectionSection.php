<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\SectionSection as SectionSectionModel;
use Lyre\Resource;

class SectionSection extends Resource
{
    public function __construct(SectionSectionModel $model)
    {
        parent::__construct($model);
    }
}
