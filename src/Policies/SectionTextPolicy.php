<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\SectionText;
use Lyre\Policy;

class SectionTextPolicy extends Policy
{
    public function __construct(SectionText $model)
    {
        parent::__construct($model);
    }
}
