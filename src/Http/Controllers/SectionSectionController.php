<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\SectionSection;
use Lyre\Content\Repositories\Contracts\SectionSectionRepositoryInterface;
use Lyre\Controller;

class SectionSectionController extends Controller
{
    public function __construct(
        SectionSectionRepositoryInterface $modelRepository
    ) {
        $model = new SectionSection();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
