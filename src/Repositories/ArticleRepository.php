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

    public function create(array $data)
    {
        if (auth()->check() && !isset($data['author_id'])) {
            $data['author_id'] = auth()->id();
        }

        $files = $data['files'] ?? null;
        unset($data['files']);

        $article = parent::create($data);

        if ($files && is_array($files)) {
            $article->resource->attachFile($files);
        }

        return $article;
    }

    public function update(array $data, string | int $slug, $thisModel = null)
    {
        if (auth()->check() && !isset($data['author_id'])) {
            $data['author_id'] = auth()->id();
        }

        $files = $data['files'] ?? null;
        unset($data['files']);

        $article = parent::update($data, $slug, $thisModel);

        if ($files !== null && is_array($files)) {
            if ($article instanceof \Illuminate\Http\Resources\Json\JsonResource) {
                $article->resource->attachFile($files);
            } else {
                $article->attachFile($files);
            }
        }

        return $article;
    }

    public function all($callbacks = [], $paginate = true)
    {
        $user = auth()->user();
        $canSeeUnpublished = $user && in_array($user->role, ['admin', 'super-admin'], true);

        if (!$canSeeUnpublished) {
            $callbacks[] = function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->where('unpublished', '!=', true)->where('published_at', '<=', now());
                    
                    if ($user) {
                        $q->orWhere('author_id', $user->id);
                    }
                });
            };
        }
        
        if (array_key_exists('facet', request()->query())) {
            $callbacks = [
                function ($query) {
                    $facet = \Lyre\Facet\Models\Facet::with('facetValues')->where('slug', request()->query('facet'))->first();
                    $facetValueIds = $facet->facetValues->pluck('id');

                    return $query->whereHas('facetValues', function ($q) use ($facetValueIds) {
                        $prefix = config('lyre.table_prefix');
                        $q->whereIn("{$prefix}facet_values.id", $facetValueIds);
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

    public function publish(string | int $slug)
    {
        $article = $this->model->where('slug', $slug)->orWhere('id', $slug)->firstOrFail();

        $article->update([
            'unpublished' => false,
            'published_at' => now(),
            'published_by' => auth()->id()
        ]);

        return $this->resource ? new $this->resource($article) : $article;
    }
}
