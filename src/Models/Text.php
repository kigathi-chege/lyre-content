<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class Text extends Model
{
    use HasFactory;

    protected $with = ['icon'];

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
