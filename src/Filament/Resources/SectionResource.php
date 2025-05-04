<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\RelationManagers\FilesRelationManager;
use Lyre\Content\Filament\Resources\SectionResource\Pages;
use Lyre\Content\Filament\Resources\SectionResource\RelationManagers;
use Lyre\Content\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
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
                    ->disabled()
                    ->helperText('This represents the name of the frontend section, should not be edited.'),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                TiptapEditor::make('title'),
                TiptapEditor::make('subtitle'),
                JsonColumn::make('misc'),
                TiptapEditor::make('description'),
                Forms\Components\Select::make('icon_id')
                    ->relationship('icon', 'name')
                    ->searchable()
                    ->preload(),
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
            ->filters([
                //
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
            FilesRelationManager::class
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
}
