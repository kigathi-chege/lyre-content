<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Content\Models\Icon;
use Lyre\Model;

class InteractionType extends Model
{
    use HasFactory;

    public function antonym()
    {
        return $this->belongsTo(self::class, 'antonym_id');
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
