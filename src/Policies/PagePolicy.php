<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Page;
use App\Models\User;
use Lyre\Policy;
use Illuminate\Auth\Access\Response;

class PagePolicy extends Policy
{
    public function __construct(Page $model)
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
