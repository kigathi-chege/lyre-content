<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Menu as MenuModel;
use Lyre\Resource;

class Menu extends Resource
{
    public function __construct(MenuModel $model)
    {
        parent::__construct($model);
    }
}
