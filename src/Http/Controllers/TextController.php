<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Text;
use Lyre\Content\Repositories\Contracts\TextRepositoryInterface;
use Lyre\Controller;

class TextController extends Controller
{
    public function __construct(
        TextRepositoryInterface $modelRepository
    ) {
        $model = new Text();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
