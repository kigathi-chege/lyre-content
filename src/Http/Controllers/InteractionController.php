<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Interaction;
use Lyre\Content\Repositories\Contracts\InteractionRepositoryInterface;
use Lyre\Controller;

class InteractionController extends Controller
{
    public function __construct(
        InteractionRepositoryInterface $modelRepository
    ) {
        $model = new Interaction();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
