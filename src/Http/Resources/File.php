<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\File as FileModel;
use Lyre\Resource;

class File extends Resource
{
    public function __construct(FileModel $model)
    {
        parent::__construct($model);
    }
}
