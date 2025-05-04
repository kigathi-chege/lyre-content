<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Page;
use App\Models\User;
use Lyre\Policy;

class PagePolicy extends Policy
{
    public function __construct(Page $model)
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
