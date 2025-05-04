<?php

namespace Lyre\Content\Http\Resources;

use Lyre\Content\Http\Resources\File;
use Lyre\Content\Models\Section as SectionModel;
use Lyre\Resource;

class Section extends Resource
{
    public function __construct(SectionModel $model)
    {
        parent::__construct($model);
    }

    // public static function loadResources($resource = null): array
    // {
    //     return [
    //         'sections' => self::class,
    //         'buttons' => Button::class,
    //         'texts' => Text::class,
    //         'files' => File::class,
    //     ];
    // }
}
