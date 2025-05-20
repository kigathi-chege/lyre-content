<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\InteractionType;
use Lyre\Content\Repositories\Contracts\InteractionTypeRepositoryInterface;
use Lyre\Controller;

class InteractionTypeController extends Controller
{
    public function __construct(
        InteractionTypeRepositoryInterface $modelRepository
    ) {
        $model = new InteractionType();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
