<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Section;
use App\Models\User;
use Lyre\Policy;
use Illuminate\Auth\Access\Response;

class SectionPolicy extends Policy
{
    public function __construct(Section $model)
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
