<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Article as ArticleModel;
use Lyre\Resource;

class Article extends Resource
{
    public function __construct(ArticleModel $model)
    {
        parent::__construct($model);
    }
}
