<?php

namespace Lyre\Content\Filament\Actions;

use Lyre\Content\Jobs\ProcessArticleUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class UploadArticlesFromFolderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'upload_articles_from_folder';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Upload Articles')
            ->icon('heroicon-o-cloud-arrow-up')
            // ->color('success')
            ->schema([
                Section::make('File Upload')
                    ->description('Upload article files, a zip archive, or multiple files at once')
                    ->schema([
                        FileUpload::make('files')
                            ->label('Article Files')
                            ->multiple()
                            ->acceptedFileTypes([
                                'text/plain',
                                'application/pdf',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword',
                                'application/zip',
                                'application/x-zip-compressed',
                            ])
                            ->maxSize(102400) // 100MB
                            ->disk('local')
                            ->directory('temp-article-uploads')
                            ->visibility('private')
                            ->preserveFilenames()
                            ->required()
                            ->helperText('Upload TXT, DOCX, PDF files, or a ZIP archive containing your articles. In supported browsers, you can also drag and drop a folder.')
                            ->columnSpanFull(),
                    ]),

                Section::make('File Types')
                    ->schema([
                        CheckboxList::make('file_types')
                            ->label('Select file types to process')
                            ->options([
                                'txt' => 'Text Files (.txt)',
                                'docx' => 'Word Documents (.docx)',
                                'pdf' => 'PDF Files (.pdf)',
                            ])
                            ->default(['txt', 'docx', 'pdf'])
                            ->required()
                            ->columns(3),
                    ]),

                Section::make('AI Assistance')
                    ->schema([
                        Toggle::make('use_ai')
                            ->label('Use AI to format articles')
                            ->default(true)
                            ->live()
                            ->helperText('Enable AI-powered formatting and enhancement'),

                        Radio::make('ai_mode')
                            ->label('AI Processing Mode')
                            ->options([
                                'format_style' => 'Use predefined format style',
                                'custom_prompt' => 'Use custom prompt',
                            ])
                            ->default('format_style')
                            ->live()
                            ->visible(fn($get) => $get('use_ai')),

                        Select::make('format_style')
                            ->label('Format Style')
                            ->options([
                                'Chicago' => 'Chicago Manual of Style',
                                'APA' => 'APA Style',
                                'MLA' => 'MLA Style',
                                'AP' => 'AP Stylebook',
                                'Conversational' => 'Conversational/Blog Style',
                            ])
                            ->default('Conversational')
                            ->visible(fn($get) => $get('use_ai') && $get('ai_mode') === 'format_style'),

                        Textarea::make('custom_prompt')
                            ->label('Custom Formatting Instructions')
                            ->rows(4)
                            ->placeholder('Enter specific instructions for how the AI should format your articles...')
                            ->helperText('Describe exactly what you want the AI to do with your content')
                            ->visible(fn($get) => $get('use_ai') && $get('ai_mode') === 'custom_prompt'),
                    ])
                    ->collapsible(),

                Section::make('AI Content Generation')
                    ->schema([
                        Checkbox::make('generate_title')
                            ->label('Generate article title')
                            ->default(true)
                            ->helperText('AI will create an engaging title based on content'),

                        Checkbox::make('generate_subtitle')
                            ->label('Generate subtitle')
                            ->default(false)
                            ->helperText('AI will create a compelling subtitle'),

                        Checkbox::make('generate_categories')
                            ->label('Generate categories')
                            ->default(true)
                            ->helperText('AI will suggest relevant categories for the article'),

                        Toggle::make('add_images')
                            ->label('Add images to articles')
                            ->default(false)
                            ->helperText('AI will add images to articles from the selected source')
                            ->live(),

                        Radio::make('image_source')
                            ->label('Image Source')
                            ->options([
                                'dalle' => 'DALL-E (AI Generated)',
                                'openverse' => 'OpenVerse (Real Images)',
                                'unsplash' => 'Unsplash (Professional Photos)',
                            ])
                            ->default('dalle')
                            ->helperText('Choose image source: AI-generated (DALL-E), curated images (OpenVerse), or professional photos (Unsplash). If primary source fails, fallback is automatic.')
                            ->visible(fn($get) => $get('add_images'))
                            ->live(),

                        TextInput::make('max_images_per_article')
                            ->label('Maximum images per article')
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(10)
                            ->visible(fn($get) => $get('add_images')),
                    ])
                    ->visible(fn($get) => $get('use_ai'))
                    ->collapsible(),

                Section::make('Publication Settings')
                    ->schema([
                        Radio::make('publish_mode')
                            ->label('Publication Date')
                            ->options([
                                'now' => 'Publish now',
                                'custom' => 'Set custom date',
                            ])
                            ->default('now')
                            ->live()
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Custom Publication Date')
                            ->visible(fn($get) => $get('publish_mode') === 'custom')
                            ->default(now()),

                        Select::make('author_id')
                            ->label('Author')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->default(Auth::id())
                            ->required()
                            ->helperText('Select the author for all uploaded articles'),
                    ]),
            ])
            ->action(function (array $data) {
                try {
                    // Prepare configuration
                    $config = [
                        'file_types' => $data['file_types'],
                        'use_ai' => $data['use_ai'] ?? false,
                        'format_style' => $data['format_style'] ?? null,
                        'custom_prompt' => $data['custom_prompt'] ?? null,
                        'generate_title' => $data['generate_title'] ?? true,
                        'generate_subtitle' => $data['generate_subtitle'] ?? false,
                        'generate_categories' => $data['generate_categories'] ?? true,
                        'add_images' => $data['add_images'] ?? false,
                        'image_source' => $data['image_source'] ?? 'dalle',
                        'max_images' => $data['max_images_per_article'] ?? 3,
                        'published_at' => $data['publish_mode'] === 'now' ? now() : $data['published_at'],
                        'author_id' => $data['author_id'],
                        'tenant_id' => tenant()?->id,
                    ];

                    // Get uploaded files
                    $uploadedFiles = $data['files'] ?? [];

                    if (empty($uploadedFiles)) {
                        throw new \Exception('No files were uploaded');
                    }

                    // Dispatch job to process articles in the background
                    ProcessArticleUpload::dispatch($uploadedFiles, $config, Auth::id());

                    // Show info notification
                    Notification::make()
                        ->info()
                        ->title('Articles Upload Started')
                        ->body("Processing " . count($uploadedFiles) . " file(s) in the background. You'll receive notifications when complete. Check logs for detailed progress.")
                        ->duration(10000) // Show for 10 seconds
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Upload Failed')
                        ->body($e->getMessage())
                        ->send();
                }
            })
            ->modalHeading('Upload Articles')
            ->modalDescription('Upload and process multiple article files with AI assistance')
            ->modalWidth('3xl')
            ->modalSubmitActionLabel('Process Articles')
            ->modalIcon('heroicon-o-cloud-arrow-up');
    }
}
