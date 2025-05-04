<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Article;
use Lyre\Content\Repositories\Contracts\ArticleRepositoryInterface;
use Lyre\Controller;

class ArticleController extends Controller
{
    public function __construct(
        ArticleRepositoryInterface $modelRepository
    ) {
        $model = new Article();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
