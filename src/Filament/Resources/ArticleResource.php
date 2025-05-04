<?php

namespace Lyre\Content\Filament\Resources;

use App\Filament\RelationManagers\FacetValuesRelationManager;
// use Lyre\Content\Filament\RelationManagers\FacetValuesRelationManager;
use Lyre\Content\Filament\RelationManagers\FilesRelationManager;
use Lyre\Content\Filament\Resources\ArticleResource\Pages;
use Lyre\Content\Filament\Resources\ArticleResource\RelationManagers;
use Lyre\Content\Models\Article;
use Lyre\Content\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'gmdi-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?int $navigationSort = 18;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextArea::make('subtitle')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('file')
                    ->label('Featured Image')
                    ->options(fn() => File::get()->pluck('name', 'id'))
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable(),
                TiptapEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'height: 500px',
                    ]),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
                Forms\Components\Toggle::make('unpublished')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\DateTimePicker::make('sent_as_newsletter_at'),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'danger' => 'unpublished',
                    ]),
                Tables\Columns\TextColumn::make('author.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FacetValuesRelationManager::class,
            FilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // $permissions = config('filament-shield.permission_prefixes.resource');
        // TODO: Kigathi - May 4 2025 - Users should only view this navigation if they have at least one more permission than view and viewAny
        return Auth::user()->can('update', Auth::user(), Article::class);
    }
}
