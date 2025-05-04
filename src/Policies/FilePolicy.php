<?php

namespace Lyre\Content\Policies;

use Lyre\Content\Models\File;
use App\Models\User;
use Lyre\Policy;

class FilePolicy extends Policy
{
    public function __construct(File $model)
    {
        parent::__construct($model);
    }
}
