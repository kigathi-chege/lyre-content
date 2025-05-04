<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class SectionText extends Model
{
    use HasFactory;

    const ORDER_COLUMN = 'order';
    const ORDER_DIRECTION = 'desc';
}
