<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\SectionButton;
use Lyre\Content\Repositories\Contracts\SectionButtonRepositoryInterface;
use Lyre\Controller;

class SectionButtonController extends Controller
{
    public function __construct(
        SectionButtonRepositoryInterface $modelRepository
    ) {
        $model = new SectionButton();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
