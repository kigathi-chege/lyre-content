<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\MenuItem;
use Lyre\Content\Repositories\Interface\MenuItemRepositoryInterface;
use Lyre\Controller;

class MenuItemController extends Controller
{
    public function __construct(
        MenuItemRepositoryInterface $modelRepository
    ) {
        $model = new MenuItem();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
