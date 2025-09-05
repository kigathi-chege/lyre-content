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

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'page_sections', 'page_id', 'section_id')
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
