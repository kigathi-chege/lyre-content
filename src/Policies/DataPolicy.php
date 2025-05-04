<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Data;
use Lyre\Policy;

class DataPolicy extends Policy
{
    public function __construct(Data $model)
    {
        parent::__construct($model);
    }
}
