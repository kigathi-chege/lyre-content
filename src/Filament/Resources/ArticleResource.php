<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Facet\Filament\RelationManagers\FacetValuesRelationManager;
use Lyre\Content\Filament\Resources\ArticleResource\Pages;
use Lyre\Content\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Lyre\File\Filament\Forms\Components\SelectFromGallery;

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
                Forms\Components\Textarea::make('subtitle')
                    ->maxLength(255)
                    ->columnSpanFull(),
                SelectFromGallery::make('files')->label('Featured Image'),
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
                Forms\Components\Select::make('categories')
                    ->relationship(
                        name: 'facetValues',
                        titleAttribute: 'name',
                        // modifyQueryUsing: fn(\Illuminate\Database\Eloquent\Builder $query) => $query->withTrashed(),
                        // TODO: Kigathi - May 18 2025 - This works because Articles is the only entity we are currently using FacetValues on
                        modifyQueryUsing: fn() => \Lyre\Facet\Models\FacetValue::query(),
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->saveRelationshipsUsing(static function ($component, $record, $state) {
                        if (!empty($state)) {
                            $record->attachFacetValues($state);
                        }
                    })
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
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            FacetValuesRelationManager::class,
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
        $usingSpatieRoles = in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses(\App\Models\User::class));
        return $usingSpatieRoles ? Auth::user()->can('update', Auth::user(), Article::class) : true;
    }
}
