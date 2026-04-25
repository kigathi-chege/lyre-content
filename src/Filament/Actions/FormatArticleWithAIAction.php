<?php

namespace Lyre\Content\Filament\Actions;

use Lyre\Content\Filament\Forms\ArticleAIFormComponents;
use Lyre\Content\Jobs\ProcessArticleFormatting;
use Lyre\Content\Models\Article;
use Filament\Notifications\Notification;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FormatArticleWithAIAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'format_with_ai';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Format with AI')
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Format Articles with AI')
            ->modalDescription('Apply AI formatting to the selected articles. This will improve readability, structure, and optionally add images.')
            ->modalSubmitActionLabel('Start Formatting')
            ->form(ArticleAIFormComponents::allSections())
            ->action(function (Collection $records, array $data) {
                $articleIds = $records->pluck('id')->toArray();

                // Prepare config
                $config = [
                    'use_ai' => true,
                    'formatting_style' => $data['formatting_style'] ?? 'conversational',
                    'custom_formatting_prompt' => $data['custom_formatting_prompt'] ?? null,
                    'regenerate_title' => $data['regenerate_title'] ?? false,
                    'regenerate_subtitle' => $data['regenerate_subtitle'] ?? false,
                    'update_categories' => $data['update_categories'] ?? true,
                    'add_images' => $data['add_images'] ?? false,
                    'image_source' => $data['image_source'] ?? 'dalle',
                    'add_featured_image' => $data['add_featured_image'] ?? true,
                    'add_inline_images' => $data['add_inline_images'] ?? true,
                ];

                // Dispatch background job
                ProcessArticleFormatting::dispatch($articleIds, $config, Auth::id());

                Notification::make()
                    ->info()
                    ->title('AI Formatting Started')
                    ->body("Processing " . count($articleIds) . " article(s) in the background...")
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
