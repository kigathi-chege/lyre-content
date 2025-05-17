<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Content\Models\Page;
use Lyre\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $with = ['icon', 'descendants'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->name && $model->page) {
                $model->name = $model->page->title;
            }
            if (!$model->link && $model->page) {
                $model->link = $model->page->link;
            }
        });
    }
}
