<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\MenuItem as MenuItemModel;
use Lyre\Resource;

class MenuItem extends Resource
{
    public function __construct(MenuItemModel $model)
    {
        parent::__construct($model);
    }
}
