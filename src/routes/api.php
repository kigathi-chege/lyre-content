<?php

use Illuminate\Support\Facades\Route;

Route::apiResources([
    'files' => \Lyre\Content\Http\Controllers\FileController::class,
    'articles' => \Lyre\Content\Http\Controllers\ArticleController::class,
    'pages' => \Lyre\Content\Http\Controllers\PageController::class,
]);

Route::get('/files/stream/{slug}.{extension}', [\Lyre\Content\Http\Controllers\FileController::class, 'stream'])->name('stream');
Route::get('/files/download/{slug}.{extension}', [\Lyre\Content\Http\Controllers\FileController::class, 'download'])->name('download');
