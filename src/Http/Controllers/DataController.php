<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Data;
use Lyre\Content\Repositories\Contracts\DataRepositoryInterface;
use Lyre\Controller;

class DataController extends Controller
{
    public function __construct(
        DataRepositoryInterface $modelRepository
    ) {
        $model = new Data();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
