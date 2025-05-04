<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\Attachment;
use Lyre\Policy;

class AttachmentPolicy extends Policy
{
    public function __construct(Attachment $model)
    {
        parent::__construct($model);
    }
}
