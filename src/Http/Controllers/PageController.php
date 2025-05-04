<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Page;
use Lyre\Content\Repositories\Contracts\PageRepositoryInterface;
use Lyre\Controller;

class PageController extends Controller
{
    public function __construct(
        PageRepositoryInterface $modelRepository
    ) {
        $model = new Page();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
