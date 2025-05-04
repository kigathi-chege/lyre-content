<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Button;
use Lyre\Content\Repositories\Contracts\ButtonRepositoryInterface;
use Lyre\Controller;

class ButtonController extends Controller
{
    public function __construct(
        ButtonRepositoryInterface $modelRepository
    ) {
        $model = new Button();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
