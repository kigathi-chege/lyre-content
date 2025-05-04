<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use App\Models\Attachment;
use Lyre\Content\Repositories\Contracts\AttachmentRepositoryInterface;

class AttachmentRepository extends Repository implements AttachmentRepositoryInterface
{
    protected $model;

    public function __construct(Attachment $model)
    {
        parent::__construct($model);
    }
}
