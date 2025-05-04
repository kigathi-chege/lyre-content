<?php

namespace Lyre\Content\Filament\Resources\SectionResource\RelationManagers;

use Lyre\Content\Filament\Resources\TextResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TextsRelationManager extends RelationManager
{
    protected static string $relationship = 'texts';

    public function form(Form $form): Form
    {
        return TextResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(TextResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
