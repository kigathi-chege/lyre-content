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

    protected $visible = ['section_data'];

    protected $hidden = ['created_at', 'updated_at', 'filters'];

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
                'facet' => $repository->relationFilters([
                    'facet' => ['column' => 'slug', 'value' => $value]
                ]),
                default => null,
            };
        }

        if (isset($orderByColumn)) {
            $repository->orderBy($orderByColumn, $orderByOrder ?? 'desc');
        }

        $results = $repository->all();

        return $results;
    }
}
