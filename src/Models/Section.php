<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\File\Concerns\HasFile;
use Lyre\Model;

class Section extends Model
{
    use HasFactory, HasFile;

    const NAME_COLUMN = 'title';
    const ORDER_COLUMN = 'order';
    const ORDER_DIRECTION = 'desc';

    protected $casts = [
        'misc' => 'array',
    ];

    protected $with = ['sections', 'buttons', 'texts', 'files', 'icon', 'data'];

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_sections', 'section_id', 'page_id');
    }

    public function sections()
    {
        return $this->belongsToMany(self::class, 'section_sections', 'parent_id', 'child_id');
    }

    public function buttons()
    {
        return $this->belongsToMany(Button::class, 'section_buttons', 'section_id', 'button_id');
    }

    public function texts()
    {
        return $this->belongsToMany(Text::class, 'section_texts', 'section_id', 'text_id');
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    public function data()
    {
        return $this->hasMany(Data::class, 'section_id');
    }
}
