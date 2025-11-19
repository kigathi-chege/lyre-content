<?php

use Lyre\Guest\Http\Middleware\EnsureGuestUser;
use Illuminate\Support\Facades\Route;
use Lyre\Content\Http\Controllers;

Route::prefix('api')
    ->middleware(['api', \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class])
    ->group(function () {
        Route::apiResources([
            'articles' => Controllers\ArticleController::class,
            'pages' => Controllers\PageController::class,
            'sections' => Controllers\SectionController::class,
            'menu' => Controllers\MenuController::class,
            'interactiontypes' => Controllers\InteractionTypeController::class,
        ]);

        Route::apiResource('interactions', Controllers\InteractionController::class)->middleware(EnsureGuestUser::class);
    });
