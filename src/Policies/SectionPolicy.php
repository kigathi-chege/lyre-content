<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Section;
use Lyre\Policy;

class SectionPolicy extends Policy
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }
}
