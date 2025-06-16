<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Menu;
use App\Models\User;
use Lyre\Policy;
use Illuminate\Auth\Access\Response;

class MenuPolicy extends Policy
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    public function viewAny(?User $user): Response
    {
        return Response::allow();
    }

    public function view(?User $user, $model): Response
    {
        return Response::allow();
    }
}
