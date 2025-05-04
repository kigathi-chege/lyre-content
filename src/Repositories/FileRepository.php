<?php

namespace Lyre\Content\Repositories;

use Lyre\Repository;
use Lyre\Content\Models\File;
use Lyre\Content\Repositories\Contracts\FileRepositoryInterface;

class FileRepository extends Repository implements FileRepositoryInterface
{
    protected $model;

    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        $thisModel = $this->uploadFile($data['file'], $data['name'] ?? null, $data['description'] ?? null);
        return $this->resource ? new $this->resource($thisModel) : $thisModel;
    }

    public function uploadFile($file, $name = null, $description = null)
    {
        $checksum = hash_file('md5', $file->getRealPath());
        $mimeType = $file->getMimeType();
        if (strpos($mimeType, 'image') !== false) {
            $resizedPaths = generate_resized_versions($file, $mimeType);
        }
        $file = File::firstOrCreate(
            ['checksum' => $checksum],
            [
                'name' => $name ?? get_file_name_without_extension($file),
                'path' => $file->store("uploads/{$mimeType}", config('filesystems.default')),
                'path_sm' =>  $resizedPaths['sm'] ?? null,
                'path_lg' =>  $resizedPaths['lg'] ?? null,
                'path_md' =>  $resizedPaths['md'] ?? null,
                'size' => $file->getSize(),
                'extension' => get_file_extension($file),
                'mimetype' => $mimeType,
                'storage' => config('filesystems.default'),
                'description' => $description
            ]
        );
        if (!$file->wasRecentlyCreated) {
            $file->increment('usagecount');
        } else {
            $file->update(['link' => route('stream', ['slug' => $file->slug, 'extension' => $file->extension])]);
        }
        return $file;
    }
}
