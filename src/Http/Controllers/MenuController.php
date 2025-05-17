<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Menu;
use Lyre\Content\Repositories\Contracts\MenuRepositoryInterface;
use Lyre\Controller;

class MenuController extends Controller
{
    public function __construct(
        MenuRepositoryInterface $modelRepository
    ) {
        $model = new Menu();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
