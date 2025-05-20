<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\Interaction;
use Lyre\Content\Repositories\Contracts\InteractionRepositoryInterface;

class InteractionRepository extends Repository implements InteractionRepositoryInterface
{
    protected $model;

    public function __construct(Interaction $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        $data['interaction_type_id'] = \Lyre\Content\Models\InteractionType::where('name', $data['type'])->first()->id;

        $data['entity_id'] = is_numeric($data['entity'])
            ? (int) $data['entity']
            : $data['entity_type']::where($data['entity_type']::ID_COLUMN, $data['entity'])->first()->id;

        unset($data['type'], $data['entity']);
        $thisModel = $this->firstOrCreate($data);
        if ($thisModel->resource->status == "deleted") {
            $thisModel->resource->status = "published";
            $thisModel->save();
        }
        if ($thisModel->interactionType->antonym_id) {
            $antonym = $this->model->where([
                'user_id' => $data['user_id'],
                'interaction_type_id' => $thisModel->interactionType->antonym_id,
                'entity_id' => $thisModel->entity_id,
                'entity_type' => $thisModel->entity_type
            ])->first();
            if ($antonym) {
                $antonym->status = "deleted";
                $antonym->save();
            }
        }
        return $thisModel;
    }
}
