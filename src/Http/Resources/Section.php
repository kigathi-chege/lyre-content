<?php

namespace Lyre\Content\Http\Resources;

use Illuminate\Http\Request;
use Lyre\Content\Models\Section as SectionModel;
use Lyre\Resource;

class Section extends Resource
{
    public function __construct(SectionModel $model)
    {
        parent::__construct($model);
    }

    public function toArray(Request $request): array
    {
        $result = parent::toArray($request);

        if (isset($result['data'])) {

            $data = [];

            foreach ($result['data'] as $item) {
                $repositoryInterface = ltrim($item->type::getRepositoryInterfaceConfig(), '\\');
                $repository = app($repositoryInterface);
                // NOTE: Kigathi - September 5 2025 - This does not yet implement filters
                $data[\Illuminate\Support\Str::lower($item->name)] = $repository->unPaginate()->all();
            }

            $result['data'] = $data;
        }

        return $result;
    }
}
