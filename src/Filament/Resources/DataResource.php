<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\DataResource\Pages;
use Lyre\Content\Models\Data;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use ValentinMorice\FilamentJsonColumn\JsonColumn;
use UnitEnum;

class DataResource extends Resource
{
    protected static ?string $model = Data::class;

    protected static \BackedEnum|string|null $navigationIcon = 'gmdi-cloud';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Content';
    }


    protected static ?int $navigationSort = 13;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('This field is used to identify the resource on the frontend. Edit with caution.'),
                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'name')
                    ->required()
                    ->visible(fn($livewire) => ! $livewire instanceof \Filament\Resources\RelationManagers\RelationManager),
                Forms\Components\Select::make('type')
                    ->options(
                        fn() => collect(get_model_classes())
                            ->mapWithKeys(fn($class) => [$class => class_basename($class)])
                            ->toArray()
                    )
                    ->searchable(),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                JsonColumn::make('filters')
                    ->default([]),
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
                Tables\Columns\TextColumn::make('section.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListData::route('/'),
            'create' => Pages\CreateData::route('/create'),
            'edit' => Pages\EditData::route('/{record}/edit'),
        ];
    }
}
