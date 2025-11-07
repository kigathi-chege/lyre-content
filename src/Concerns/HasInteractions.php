<?php

namespace Lyre\Content\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lyre\Content\Models\Interaction;

trait HasInteractions
{
    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'entity');
    }
}
