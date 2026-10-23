<?php

namespace Lyre\Content\Repositories;

use Illuminate\Support\Facades\Gate;
use Lyre\Exceptions\CommonException;
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

        if (!$this->canSeeUnpublished($user)) {
            $callbacks[] = function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->where('unpublished', '!=', true)->where('published_at', '<=', now());

                    if ($user) {
                        $q->orWhere('author_id', $user->id);
                    }
                });
            };
        }

        // Added alongside the visibility filter; replacing $callbacks here used
        // to drop it and expose drafts to anyone passing ?facet=.
        if (array_key_exists('facet', request()->query())) {
            $callbacks[] = function ($query) {
                $facet = \Lyre\Facet\Models\Facet::with('facetValues')->where('slug', request()->query('facet'))->first();

                if (!$facet) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereHas('facetValues', function ($q) use ($facet) {
                    $prefix = config('lyre.table_prefix');
                    $q->whereIn("{$prefix}facet_values.id", $facet->facetValues->pluck('id'));
                });
            };
        }
        $this->model::setExcludedSerializableColumns(['content']);
        return parent::all($callbacks, $paginate);
    }

    public function find($arguments, $callbacks = [])
    {
        $articleResource = parent::find($arguments, $callbacks);
        if ($articleResource === null) {
            return null;
        }

        $article = $articleResource->resource ?? $articleResource;
        $user = auth()->user();
        $isAuthor = $user && $article->author_id == $user->id;
        $canSeeUnpublished = $this->canSeeUnpublished($user);

        // Drafts and scheduled posts look missing to everyone but their author and editors.
        if ($article->status !== 'published' && !$isAuthor && !$canSeeUnpublished) {
            if ($this->silent) {
                return null;
            }
            throw CommonException::fromMessage('Article not found for arguments: ' . json_encode($arguments), 404);
        }

        // Count reader views only, and without touching updated_at or firing model events.
        if ($article->status === 'published' && !$isAuthor && !$canSeeUnpublished) {
            $article->newQuery()->whereKey($article->getKey())->toBase()->increment('views');
            $article->views = ($article->views ?? 0) + 1;
        }

        return $articleResource;
    }

    /** Admins, and anyone allowed to update articles, see drafts and scheduled posts. */
    protected function canSeeUnpublished($user): bool
    {
        if (!$user) {
            return false;
        }

        if (in_array($user->role ?? null, ['admin', 'super-admin'], true)) {
            return true;
        }

        return Gate::forUser($user)->allows('update', $this->model);
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
