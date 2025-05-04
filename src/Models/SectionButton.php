<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class SectionButton extends Model
{
    use HasFactory;

    const ORDER_COLUMN = 'order';
    const ORDER_DIRECTION = 'desc';
}
