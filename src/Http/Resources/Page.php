<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Page as PageModel;
use Lyre\Resource;

class Page extends Resource
{
    public function __construct(PageModel $model)
    {
        parent::__construct($model);
    }
}
