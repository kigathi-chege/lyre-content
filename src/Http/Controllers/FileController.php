<?php

namespace Lyre\Content\Http\Controllers;

use Lyre\Content\Models\File;
use Lyre\Content\Repositories\Contracts\FileRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Lyre\Controller;
use Lyre\Request as BaseRequest;

class FileController extends Controller
{
    public function __construct(
        FileRepositoryInterface $modelRepository
    ) {
        $model = new File();
        $modelConfig = $model->generateConfig();
        parent::__construct($modelConfig, $modelRepository);
    }

    public function stream($slug, $extension)
    {
        $attachment = File::where("slug", $slug)
            ->select([
                "id",
                "name",
                "path",
                "viewed_at",
                "storage",
            ])
            ->first();
        $attachment->viewed_at = now();
        $attachment->save();
        $stream = Storage::disk($attachment->storage)->readStream($attachment->path);
        $cacheDuration = 6 * 30 * 24 * 60 * 60;
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => Storage::disk($attachment->storage)->mimeType($attachment->path),
            'Cache-Control' => 'public, max-age=' . $cacheDuration,
            'Expires' => gmdate("D, d M Y H:i:s", time() + $cacheDuration),
        ]);
    }

    public function download($slug, $extension)
    {
        $attachment = File::where("slug", $slug)
            ->select([
                "id",
                "name",
                "path",
                "viewed_at",
                "storage",
            ])
            ->first();
        $attachment->viewed_at = now();
        $attachment->save();
        $filePath = Storage::disk($attachment->storage)->url($attachment->path);
        header("Cache-Control: public, max-age=31536000");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000));
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$attachment->name}");
        header("Content-Type: " . Storage::disk($attachment->storage)->mimeType($attachment->path));
        return readfile($filePath);
    }
}
