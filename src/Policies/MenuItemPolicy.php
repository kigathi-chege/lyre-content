<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\MenuItem;
use Lyre\Policy;

class MenuItemPolicy extends Policy
{
    public function __construct(MenuItem $model)
    {
        parent::__construct($model);
    }
}
