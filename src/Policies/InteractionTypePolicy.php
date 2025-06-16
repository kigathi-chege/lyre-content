<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\InteractionType;
use App\Models\User;
use Lyre\Policy;
use Illuminate\Auth\Access\Response;

class InteractionTypePolicy extends Policy
{
    public function __construct(InteractionType $model)
    {
        parent::__construct($model);
    }

    public function viewAny(?User $user): Response
    {
        return Response::allow();
    }
}
