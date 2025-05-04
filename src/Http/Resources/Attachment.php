<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Models\Attachment as AttachmentModel;
use Lyre\Resource;

class Attachment extends Resource
{
    public function __construct(AttachmentModel $model)
    {
        parent::__construct($model);
    }
}
