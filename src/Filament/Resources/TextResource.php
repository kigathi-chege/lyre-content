<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\TextResource\Pages;
use Lyre\Content\Models\Text;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\HtmlString;
use UnitEnum;

class TextResource extends Resource
{
    protected static ?string $model = Text::class;

    protected static \BackedEnum|string|null $navigationIcon = 'gmdi-edit-note';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Content';
    }

    protected static ?int $navigationSort = 11;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('This field is used to identify the resource on the frontend. Edit with caution.'),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
                TiptapEditor::make('content')
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
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->formatStateUsing(fn(Text $record): HtmlString => $record->icon ? new HtmlString($record->icon->content) : ''),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTexts::route('/'),
            'edit' => Pages\EditText::route('/{record}/edit'),
        ];
    }
}
