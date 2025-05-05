<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class)
    ->group(function () {
        Route::apiResources([
            'articles' => \Lyre\Content\Http\Controllers\ArticleController::class,
            'pages' => \Lyre\Content\Http\Controllers\PageController::class,
        ]);
    });
