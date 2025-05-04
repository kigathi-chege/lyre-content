<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Button;
use Lyre\Policy;

class ButtonPolicy extends Policy
{
    public function __construct(Button $model)
    {
        parent::__construct($model);
    }
}
