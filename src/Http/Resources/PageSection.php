<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\PageSection as PageSectionModel;
use Lyre\Resource;

class PageSection extends Resource
{
    public function __construct(PageSectionModel $model)
    {
        parent::__construct($model);
    }
}
