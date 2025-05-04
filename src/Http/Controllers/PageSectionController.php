<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\PageSection;
use Lyre\Content\Repositories\Contracts\PageSectionRepositoryInterface;
use Lyre\Controller;

class PageSectionController extends Controller
{
    public function __construct(
        PageSectionRepositoryInterface $modelRepository
    ) {
        $model = new PageSection();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
