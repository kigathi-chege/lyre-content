<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Facet\Concerns\HasFacet;
use Lyre\Model;

class Page extends Model
{
    use HasFactory, HasFacet;

    const ID_COLUMN = 'slug';
    const NAME_COLUMN = 'title';
    const ORDER_COLUMN = 'order';
    const ORDER_DIRECTION = 'asc';

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'page_sections', 'page_id', 'section_id')
            ->withPivot('order')
            ->orderBy('pivot_order');
    }
}
