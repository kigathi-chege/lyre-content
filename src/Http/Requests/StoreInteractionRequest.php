<?php

namespace Lyre\Content\Http\Requests;

use Lyre\Request;

class StoreInteractionRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $entityTypes = join(',', get_model_classes());
        $interactionTypes = join(',', \Lyre\Content\Models\InteractionType::all()->pluck('name')->toArray());

        return [
            // 'interaction_type_id' => ['required', 'integer', 'exists:interaction_types,id'],
            'type' => ['required', 'string', "in:{$interactionTypes}"],
            'content' => ['nullable', 'string'],
            'entity' => ['required', 'regex:/^[0-9]+$|^[A-Za-z0-9_\- ]+$/'],
            'entity_type' => ['required', 'string', "in:{$entityTypes}"],
        ];
    }
}
