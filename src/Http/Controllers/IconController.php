<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Icon;
use Lyre\Content\Repositories\Contracts\IconRepositoryInterface;
use Lyre\Controller;

class IconController extends Controller
{
    public function __construct(
        IconRepositoryInterface $modelRepository
    ) {
        $model = new Icon();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
