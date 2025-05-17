<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Menu;
use App\Models\User;
use Lyre\Policy;

class MenuPolicy extends Policy
{
    public function __construct(Menu $model)
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
