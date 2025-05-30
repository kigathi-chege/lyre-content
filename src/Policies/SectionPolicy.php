<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Section;
use App\Models\User;
use Lyre\Policy;

class SectionPolicy extends Policy
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, $model): bool
    {
        return true;
    }
}
