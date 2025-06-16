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

    public function all($callbacks = [], $paginate = true)
    {
        $callbacks[] = fn($query) => $query->where('unpublished', '!=', true)->where('published_at', '<=', now());
        if (array_key_exists('facet', request()->query())) {
            $callbacks = [
                function ($query) {
                    $facet = \Lyre\Facet\Models\Facet::with('facetValues')->where('slug', request()->query('facet'))->first();
                    $facetValueIds = $facet->facetValues->pluck('id');

                    return $query->whereHas('facetValues', function ($q) use ($facetValueIds) {
                        $q->whereIn('facet_values.id', $facetValueIds);
                    });
                }
            ];
        }
        $this->model::setExcludedSerializableColumns(['content']);
        return parent::all($callbacks, $paginate);
    }

    public function find($arguments, $callbacks = [])
    {
        $articleResource = parent::find($arguments, $callbacks);
        $articleResource->resource->update(['views' => $articleResource->resource->views + 1]);

        return $articleResource;
    }
}
