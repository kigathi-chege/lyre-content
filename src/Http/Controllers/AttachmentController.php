<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\Attachment;
use Lyre\Content\Repositories\Contracts\AttachmentRepositoryInterface;
use Lyre\Controller;

class AttachmentController extends Controller
{
    public function __construct(
        AttachmentRepositoryInterface $modelRepository
    ) {
        $model = new Attachment();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }
}
