<?php

namespace Lyre\Content\Filament\Actions;

use Lyre\Content\Filament\Forms\ArticleAIFormComponents;
use Lyre\Content\Jobs\ProcessArticleFormatting;
use Lyre\Content\Models\Article;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class FormatSingleArticleWithAIAction extends Action
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
            ->modalHeading('Format Article with AI')
            ->modalDescription('Apply AI formatting to improve readability, structure, and optionally add images.')
            ->modalSubmitActionLabel('Start Formatting')
            ->form(ArticleAIFormComponents::allSections([
                'formatting' => ['defaultStyle' => 'conversational'],
                'enhancements' => [
                    'regenerate_title' => false,
                    'regenerate_subtitle' => false,
                    'update_categories' => true,
                ],
                'images' => [
                    'add_images' => false,
                    'add_featured_image' => true,
                    'add_inline_images' => true,
                ],
            ]))
            ->action(function (Article $record, array $data) {
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

                // Dispatch background job for single article
                ProcessArticleFormatting::dispatch([$record->id], $config, Auth::id());

                Notification::make()
                    ->info()
                    ->title('AI Formatting Started')
                    ->body("Formatting article \"{$record->title}\" in the background...")
                    ->send();
            })
            ->visible(fn (Article $record): bool => !$record->is_ai_formatted);
    }
}
