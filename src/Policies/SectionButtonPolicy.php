<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\SectionButton;
use Lyre\Policy;

class SectionButtonPolicy extends Policy
{
    public function __construct(SectionButton $model)
    {
        parent::__construct($model);
    }
}
