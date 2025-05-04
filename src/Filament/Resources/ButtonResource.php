<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\ButtonResource\Pages;
use Lyre\Content\Filament\Resources\ButtonResource\RelationManagers;
use Lyre\Content\Models\Button;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ButtonResource extends Resource
{
    protected static ?string $model = Button::class;

    protected static ?string $navigationIcon = 'heroicon-o-stop';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('This field is used to identify the resource on the frontend. Should not be changed.'),
                Forms\Components\TextInput::make('text')
                    ->maxLength(255),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->formatStateUsing(fn(Button $record): HtmlString => $record->icon ? new HtmlString($record->icon->content) : ''),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListButtons::route('/'),
            'edit' => Pages\EditButton::route('/{record}/edit'),
        ];
    }
}
