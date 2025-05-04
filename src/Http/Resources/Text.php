<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Text as TextModel;
use Lyre\Resource;

class Text extends Resource
{
    public function __construct(TextModel $model)
    {
        parent::__construct($model);
    }
}
