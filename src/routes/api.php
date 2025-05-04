<?php

use Illuminate\Support\Facades\Route;

Route::apiResources([
    'articles' => \Lyre\Content\Http\Controllers\ArticleController::class,
    'pages' => \Lyre\Content\Http\Controllers\PageController::class,
]);
