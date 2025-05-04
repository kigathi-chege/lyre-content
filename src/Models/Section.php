<?php

namespace Lyre\Content\Models;

use Lyre\File\Models\Attachment;
use Lyre\File\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Model;

class Section extends Model
{
    use HasFactory;

    const NAME_COLUMN = 'title';
    const ORDER_COLUMN = 'order';
    const ORDER_DIRECTION = 'desc';

    protected $casts = [
        'misc' => 'array',
    ];

    protected $with = ['sections', 'buttons', 'texts', 'files', 'icon'];

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

    public function files()
    {
        return $this->hasManyThrough(File::class, Attachment::class, 'attachable_id', 'id', 'id', 'file_id')
            ->where('attachments.attachable_type', self::class);
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
