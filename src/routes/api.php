<?php

use Illuminate\Support\Facades\Route;
use Lyre\Content\Http\Controllers;

Route::prefix('api')
    ->middleware(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class)
    ->group(function () {
        Route::apiResources([
            'articles' => Controllers\ArticleController::class,
            'pages' => Controllers\PageController::class,
            'sections' => Controllers\SectionController::class,
            'menu' => Controllers\MenuController::class,
            'interactions' => Controllers\InteractionController::class,
            'interactiontypes' => Controllers\InteractionTypeController::class,
        ]);
    });
