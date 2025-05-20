<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Lyre\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $with = ['user', 'interactionType'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interactionType()
    {
        return $this->belongsTo(InteractionType::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
