<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class Data extends Model
{
    use HasFactory;

    protected $casts = [
        'filters' => 'array',
    ];

    protected array $included = ['section_data'];

    protected array $excluded = ['created_at', 'updated_at'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function getSectionDataAttribute()
    {
        $model = $this->type;

        if ($model == "\\" . Article::class) {
            $model::setExcludedSerializableColumns(['content']);
        }

        $repository = $model::resolveRepository();
        foreach ($this->filters as $key => $value) {
            match ($key) {
                'orderByColumn' => $orderByColumn = $value,
                'orderByOrder' => $orderByOrder = $value,
                'limit' => $repository->limit((int)$value),
                'offset' => $repository->offset((int)$value),
                'unpaginated' => $value ? $repository->unPaginate() : null,
                default => null,
            };
        }

        if (array_key_exists('relation', $this->filters)) {
            $parts = explode(",", $this->filters['relation']);
            $result = [];
            $modelInstance = new $model;
            for ($i = 0; $i < count($parts); $i += 2) {
                if ($parts[$i]) {
                    $relatedModel = $modelInstance->{$parts[$i]}();
                    $relatedModelClass = get_class($relatedModel->getRelated());
                    $idColumn = $relatedModelClass::ID_COLUMN;
                    $idTable = (new $relatedModelClass)->getTable();
                    $result[$parts[$i]] = [
                        'column' => "$idTable.$idColumn",
                        'value' => $parts[$i + 1],
                    ];
                }
            }

            $repository->relationFilters($result);
        }

        $callbacks = null;
        if (array_key_exists('facet', $this->filters)) {
            $callbacks = [
                function ($query) {
                    $facet = \Lyre\Facet\Models\Facet::with('facetValues')->where('slug', $this->filters['facet'])->first();
                    $facetValueIds = $facet->facetValues->pluck('id');

                    return $query->whereHas('facetValues', function ($q) use ($facetValueIds) {
                        $q->whereIn('facet_values.id', $facetValueIds);
                    });
                }
            ];
        }

        if (isset($orderByColumn)) {
            $repository->orderBy($orderByColumn, $orderByOrder ?? 'desc');
        }

        $results = $repository->all($callbacks);

        return $results;
    }
}
