<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\SectionText;
use Lyre\Content\Repositories\Contracts\SectionTextRepositoryInterface;
use Lyre\Controller;

class SectionTextController extends Controller
{
    public function __construct(
        SectionTextRepositoryInterface $modelRepository
    ) {
        $model = new SectionText();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
