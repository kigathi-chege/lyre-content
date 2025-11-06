<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\File\Concerns\HasFile;
use Lyre\Model;

class Section extends Model
{
    use HasFactory, HasFile;

    const NAME_COLUMN = 'name';

    protected $casts = [
        'misc' => 'array',
    ];

    protected $with = ['sections', 'buttons', 'texts', 'files', 'icon', 'data'];

    public function pages()
    {
        $prefix = config('lyre.table_prefix');
        return $this->belongsToMany(Page::class, "{$prefix}page_sections", 'section_id', 'page_id');
    }

    public function sections()
    {
        $prefix = config('lyre.table_prefix');
        return $this->belongsToMany(self::class, "{$prefix}section_sections", 'parent_id', 'child_id');
    }

    public function buttons()
    {
        $prefix = config('lyre.table_prefix');
        return $this->belongsToMany(Button::class, "{$prefix}section_buttons", 'section_id', 'button_id');
    }

    public function texts()
    {
        $prefix = config('lyre.table_prefix');
        return $this->belongsToMany(Text::class, "{$prefix}section_texts", 'section_id', 'text_id');
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
