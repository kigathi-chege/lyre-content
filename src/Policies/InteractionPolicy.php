<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Interaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lyre\Policy;

class InteractionPolicy extends Policy
{
    public function __construct(Interaction $model)
    {
        parent::__construct($model);
    }

    public function create(?User $user): bool
    {
        return Auth::check();
    }
}
