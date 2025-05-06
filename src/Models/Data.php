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

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
