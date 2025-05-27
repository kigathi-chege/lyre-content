<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class Button extends Model
{
    use HasFactory;

    protected $with = ['icon'];

    protected $casts = [
        'misc' => 'array',
    ];

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
