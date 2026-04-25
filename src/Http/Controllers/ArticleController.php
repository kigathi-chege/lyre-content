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

    public function publish(string $slug)
    {
        $this->localAuthorize('update', $slug);
        
        return __response(
            true,
            'Article published successfully',
            $this->modelRepository->publish($slug),
            200
        );
    }
}
