<?php

namespace Lyre\Content\Filament\Forms;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class ArticleAIFormComponents
{
    /**
     * Get AI formatting options section
     */
    public static function formattingSection(array $options = []): Section
    {
        $showCustomField = $options['showCustomField'] ?? true;
        $defaultStyle = $options['defaultStyle'] ?? 'conversational';

        return Section::make('AI Formatting Options')
            ->description('Configure how the AI should format your articles')
            ->schema([
                Radio::make('formatting_style')
                    ->label('Formatting Style')
                    ->options([
                        'chicago' => 'Chicago Manual of Style',
                        'apa' => 'APA Style',
                        'mla' => 'MLA Style',
                        'ap' => 'AP Style',
                        'conversational' => 'Conversational/Blog Style',
                        'custom' => 'Custom Prompt',
                    ])
                    ->default($defaultStyle)
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                Textarea::make('custom_formatting_prompt')
                    ->label('Custom Formatting Instructions')
                    ->placeholder('Describe how you want the articles formatted...')
                    ->rows(4)
                    ->visible(fn($get) => $showCustomField && $get('formatting_style') === 'custom')
                    ->required(fn($get) => $get('formatting_style') === 'custom')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Get AI enhancements section
     */
    public static function enhancementsSection(array $defaults = []): Section
    {
        return Section::make('AI Enhancements')
            ->description('Additional AI-powered improvements')
            ->schema([
                Toggle::make('regenerate_title')
                    ->label('Regenerate Title')
                    ->helperText('Let AI create a more compelling title')
                    ->default($defaults['regenerate_title'] ?? false),

                Toggle::make('regenerate_subtitle')
                    ->label('Regenerate Subtitle')
                    ->helperText('Let AI create an engaging subtitle')
                    ->default($defaults['regenerate_subtitle'] ?? false),

                Toggle::make('update_categories')
                    ->label('Update Categories')
                    ->helperText('Let AI suggest better category assignments')
                    ->default($defaults['update_categories'] ?? true),
            ])
            ->columns(1);
    }

    /**
     * Get image management section
     */
    public static function imageSection(array $defaults = []): Section
    {
        return Section::make('Image Management')
            ->description('Add images to your articles from AI generation or real image databases')
            ->schema([
                Toggle::make('add_images')
                    ->label('Add Images')
                    ->helperText('Add relevant images to articles')
                    ->default($defaults['add_images'] ?? false)
                    ->live(),

                Radio::make('image_source')
                    ->label('Image Source')
                    ->options([
                        'dalle' => 'DALL-E (AI Generated)',
                        'openverse' => 'OpenVerse (Real Images)',
                        'unsplash' => 'Unsplash (Professional Photos)',
                    ])
                    ->default($defaults['image_source'] ?? 'dalle')
                    ->helperText('Choose image source: AI-generated (DALL-E), curated images (OpenVerse), or professional photos (Unsplash). If primary source fails, fallback is automatic.')
                    ->visible(fn($get) => $get('add_images'))
                    ->live()
                    ->columnSpanFull(),

                Checkbox::make('add_featured_image')
                    ->label('Add Featured Image')
                    ->helperText('Add a featured image for the article')
                    ->default($defaults['add_featured_image'] ?? true)
                    ->visible(fn($get) => $get('add_images')),

                Checkbox::make('add_inline_images')
                    ->label('Add Inline Images')
                    ->helperText('Add relevant images at natural break points (between paragraphs, sections, or sentences - never mid-sentence)')
                    ->default($defaults['add_inline_images'] ?? true)
                    ->visible(fn($get) => $get('add_images')),
            ]);
    }

    /**
     * Get all AI-related form sections
     */
    public static function allSections(array $options = []): array
    {
        $formattingOptions = $options['formatting'] ?? [];
        $enhancementDefaults = $options['enhancements'] ?? [];
        $imageDefaults = $options['images'] ?? [];

        return [
            self::formattingSection($formattingOptions),
            self::enhancementsSection($enhancementDefaults),
            self::imageSection($imageDefaults),
        ];
    }

    /**
     * Get compact AI options (for inline actions)
     */
    public static function compactOptions(array $defaults = []): array
    {
        return [
            Toggle::make('use_ai')
                ->label('Use AI Formatting')
                ->helperText('Apply AI-powered formatting to improve readability')
                ->default($defaults['use_ai'] ?? true)
                ->live(),

            Radio::make('formatting_style')
                ->label('Style')
                ->options([
                    'conversational' => 'Conversational',
                    'professional' => 'Professional',
                    'custom' => 'Custom',
                ])
                ->default('conversational')
                ->inline()
                ->visible(fn($get) => $get('use_ai'))
                ->columnSpanFull(),

            Textarea::make('custom_formatting_prompt')
                ->label('Custom Instructions')
                ->placeholder('Describe formatting preferences...')
                ->rows(2)
                ->visible(fn($get) => $get('use_ai') && $get('formatting_style') === 'custom')
                ->columnSpanFull(),

            Toggle::make('add_images')
                ->label('Add AI Images')
                ->helperText('Generate and add relevant images')
                ->default($defaults['add_images'] ?? false)
                ->visible(fn($get) => $get('use_ai')),
        ];
    }
}
