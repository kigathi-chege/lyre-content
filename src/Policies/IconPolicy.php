<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Icon;
use Lyre\Policy;

class IconPolicy extends Policy
{
    public function __construct(Icon $model)
    {
        parent::__construct($model);
    }
}
