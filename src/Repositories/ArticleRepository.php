<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Article;
use Lyre\Content\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleRepository extends Repository implements ArticleRepositoryInterface
{
    protected $model;

    public function __construct(Article $model)
    {
        parent::__construct($model);
    }

    public function all($filterCallback = null, $paginate = true)
    {
        $filterCallback = fn($query) => $query->where('unpublished', '!=', true)->where('published_at', '<=', now());
        $this->model::setExcludedSerializableColumns(['content']);
        return parent::all($filterCallback, $paginate);
    }

    public function find($arguments, $filterCallback = null)
    {
        return parent::find($arguments, $filterCallback);
    }
}
