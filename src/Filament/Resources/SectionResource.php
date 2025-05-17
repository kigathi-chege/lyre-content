<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\SectionResource\Pages;
use Lyre\Content\Filament\Resources\SectionResource\RelationManagers;
use Lyre\Content\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\HtmlString;
use Lyre\File\Filament\Forms\Components\SelectFromGallery;
use ValentinMorice\FilamentJsonColumn\JsonColumn;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'gmdi-grid-view';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('This represents the name of the frontend section, should be edited with caution.'),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                SelectFromGallery::make('files')->label('Featured Images')->multiple(),
                Forms\Components\Select::make('icon_id')
                    ->relationship('icon', 'name')
                    ->searchable()
                    ->preload(),
                TiptapEditor::make('title'),
                TiptapEditor::make('subtitle'),
                TiptapEditor::make('description'),
                JsonColumn::make('misc'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->formatStateUsing(fn(Section $record): HtmlString => $record->icon ? new HtmlString($record->icon->content) : ''),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ButtonsRelationManager::class,
            RelationManagers\TextsRelationManager::class,
            RelationManagers\SectionsRelationManager::class,
            RelationManagers\DataRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // $permissions = config('filament-shield.permission_prefixes.resource');
        // TODO: Kigathi - May 4 2025 - Users should only view this navigation if they have at least one more permission than view and viewAny
        $usingSpatieRoles = in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses(\App\Models\User::class));
        return $usingSpatieRoles ? \Illuminate\Support\Facades\Auth::user()->can('update', new Section) : true;
    }
}
