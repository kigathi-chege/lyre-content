<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Article;
use App\Models\User;
use Lyre\Policy;

class ArticlePolicy extends Policy
{
    public function __construct(Article $model)
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
