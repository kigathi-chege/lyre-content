<?php

namespace Lyre\Content\Observers;

use Lyre\Content\Models\MenuItem;
use Lyre\Content\Models\Page;
use Lyre\Observer;

class MenuItemObserver extends Observer
{
    public function creating($model)
    {
        if (!$model->name && $model->page_id) {
            $model->name = Page::find($model->page_id)->title;
        }

        if (!$model->link && $model->page_id) {
            $model->link = Page::find($model->page_id)->link;
        }

        parent::creating($model);
    }
}
