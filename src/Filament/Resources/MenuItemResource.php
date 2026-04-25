<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\MenuItemResource\Pages;
use Lyre\Content\Filament\Resources\MenuItemResource\RelationManagers;
use Lyre\Content\Models\MenuItem;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\HtmlString;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'gmdi-list';

    public static function getNavigationGroup(): ?string
    {
        return 'Organization';
    }


    protected static ?int $navigationSort = 51;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->helperText("If you leave this blank, it will be populated with the page's title"),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255)
                    ->required(fn(Get $get): bool => $get('is_external'))
                    ->helperText(new HtmlString("If you leave this blank, it will be populated with the page's link. </br> This is <strong>required</strong> for external links.")),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('menu_id')
                    ->relationship('menu', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn($livewire) => ! $livewire instanceof \Filament\Resources\RelationManagers\RelationManager),
                Forms\Components\Select::make('page_id')
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('icon_id')
                    ->relationship('icon', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_external')
                    ->required()
                    ->live()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('menu.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('page.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_external')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
