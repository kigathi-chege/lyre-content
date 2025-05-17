<?php

namespace Lyre\Content\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lyre\Content\Http\Resources\MenuItem;
use Lyre\Content\Models\MenuItem as ModelsMenuItem;
use Lyre\Model;

class Menu extends Model
{
    use HasFactory;

    protected array $included = ['filtered_menu_items'];

    public function menuItems()
    {
        return $this->hasMany(ModelsMenuItem::class);
    }

    public function getFilteredMenuItemsAttribute()
    {
        return MenuItem::collection(ModelsMenuItem::where('menu_id', $this->id)->whereNull('parent_id')->with('descendants')->get());
    }
}
