<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Section;
use Lyre\Content\Repositories\Contracts\SectionRepositoryInterface;
use Lyre\Controller;

class SectionController extends Controller
{
    public function __construct(
        SectionRepositoryInterface $modelRepository
    ) {
        $model = new Section();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
