<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Text;
use Lyre\Policy;

class TextPolicy extends Policy
{
    public function __construct(Text $model)
    {
        parent::__construct($model);
    }
}
