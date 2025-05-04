<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class File extends Model
{
    use HasFactory;

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
